<?php

namespace App\Services;

use App\Models\{Tax, TaxRule};
use Carbon\Carbon;

class TaxCalculatorService
{
    public function calcular(array $contexto): array
    {
        $data = data_get($contexto, 'data') ?: now()->toDateString();
        $ignorarSegmento = (bool) data_get($contexto, 'ignorar_segmento', false);

        // escopos recebidos
        $ufCli = data_get($contexto, 'clientes.uf');
        $tipoOp = data_get($contexto, 'operacao.tipo', 'venda');
        $canal = data_get($contexto, 'operacao.canal');
        $ufO = data_get($contexto, 'operacao.uf_origem');
        $ufD = data_get($contexto, 'operacao.uf_destino', $ufCli);

        // produto
        $catId = (int) data_get($contexto, 'produto.categoria_id');
        $produtoId = (int) data_get($contexto, 'produto.id');
        $ncm = data_get($contexto, 'produto.ncm');

        // valores (→ estavam faltando antes)
        $valor = (float) data_get($contexto, 'valores.valor', 0.0);
        $desconto = (float) data_get($contexto, 'valores.desconto', 0.0);
        $frete = (float) data_get($contexto, 'valores.frete', 0.0);

        $dataRef = $data;

        // === SUBQUERY: calcula o melhor alvo por regra (1 linha por tr.id) ===
        $sub = \DB::table('tax_rules as tr') //Mudar isso, evitar usar DB
            ->leftJoin('tax_rule_alvos as tra', 'tra.tax_rule_id', '=', 'tr.id')
            ->where(function ($q) use ($dataRef) {
                $q->whereNull('tr.vigencia_inicio')
                    ->orWhere('tr.vigencia_inicio', '<=', $dataRef);
            })
            ->where(function ($q) use ($dataRef) {
                $q->whereNull('tr.vigencia_fim')
                    ->orWhere('tr.vigencia_fim', '>=', $dataRef);
            })
            ->where(function ($q) use ($produtoId, $catId) {
                // aceita genérico (sem alvo) OU alvo por produto/categoria
                $q->whereNull('tra.id')
                    ->orWhere('tra.id_produto_fk', '=', $produtoId)
                    ->orWhere('tra.id_categoria_fk', '=', $catId);
            })
            ->groupBy('tr.id')
            ->selectRaw('
            tr.id as rule_id,
            MAX(
                CASE
                    WHEN tra.id_produto_fk = ? THEN 2
                    WHEN tra.id_categoria_fk = ? THEN 1
                    WHEN tra.id IS NULL      THEN 0
                    ELSE 0
                END
            ) as _match_score
        ', [$produtoId, $catId]);

        // === SELECT principal (sem GROUP BY) ===
        $linhas = \DB::table('tax_rules as tr') //Mudar isso, evitar usar DB
            ->joinSub($sub, 'm', function ($join) {
                $join->on('m.rule_id', '=', 'tr.id');
            })
            ->join('taxes as t', 't.id', '=', 'tr.tax_id')
            ->where('t.ativo', true)
            ->select([
                't.id as tax_id',
                't.codigo',
                't.nome',
                't.ativo',
                'tr.*',
                'm._match_score',
            ])
            ->get();
              
        // Agrupa por imposto
        $porImposto = $linhas->groupBy('tax_id');

        $resultado = [];

        // Para cada IMPOSTO, filtra as regras pelo contexto e aplica
        foreach ($porImposto as $taxId => $regras) {
         
            $meta = $regras->first();
            $taxCodigo = $meta->codigo;
            $taxNome = $meta->nome;
            
            // 1) Filtro contextual (escopo, UF, canal, NCM etc.)
            $candidatas = $regras->filter(function ($r) use ($ignorarSegmento, $ncm, $ufO, $ufD, $canal, $tipoOp) {

                // escopo 1 = venda/saída (ajuste se usar outros)
                if ((int) ($r->escopo ?? 1) !== 1)
                    return false;

                if (!$ignorarSegmento && $r->segment_id !== null)
                    return false;

                // if (!is_null($r->ncm_padrao) && (string) $r->ncm_padrao !== (string) $ncm)
                //     return false;

                $ufOriRegra = $r->uf_origem ? (string) $r->uf_origem : null;
                $ufDesRegra = $r->uf_destino ? (string) $r->uf_destino : null;
                if (!is_null($ufOriRegra) && $ufOriRegra !== $ufO)
                    return false;
                if (!is_null($ufDesRegra) && $ufDesRegra !== $ufD)
                    return false;

                // if (!is_null($r->canal) && (string) $r->canal !== (string) $canal)
                //     return false;
                // if (!is_null($r->tipo_operacao) && (string) $r->tipo_operacao !== (string) $tipoOp)
                //     return false;

                return true;
            });
            if ($candidatas->isEmpty())
                continue;

            // 2) Ordenação: alvo (produto > categoria > genérico), depois especificidade, depois prioridade
            $candidatas = $candidatas->sort(function ($a, $b) {
                $ma = (int) ($a->_match_score ?? 0);
                $mb = (int) ($b->_match_score ?? 0);
                if ($ma !== $mb)
                    return $mb <=> $ma;

                $sa = (
                    (!is_null($a->segment_id) ? 1 : 0) +
                    (!is_null($a->ncm_padrao) ? 1 : 0) +
                    (!is_null($a->uf_origem) ? 1 : 0) +
                    (!is_null($a->uf_destino) ? 1 : 0) +
                    (!is_null($a->canal) ? 1 : 0) +
                    (!is_null($a->tipo_operacao) ? 1 : 0)
                );
                $sb = (
                    (!is_null($b->segment_id) ? 1 : 0) +
                    (!is_null($b->ncm_padrao) ? 1 : 0) +
                    (!is_null($b->uf_origem) ? 1 : 0) +
                    (!is_null($b->uf_destino) ? 1 : 0) +
                    (!is_null($b->canal) ? 1 : 0) +
                    (!is_null($b->tipo_operacao) ? 1 : 0)
                );
                if ($sa !== $sb)
                    return $sb <=> $sa;

                return ((int) $a->prioridade) <=> ((int) $b->prioridade);
            });

            // 3) Aplica as regras (respeita cumulatividade)
            $aplicadas = [];
            foreach ($candidatas as $ruleRow) {
                [$base, $valorImposto, $aliq] = $this->avaliarRegraFromRow($ruleRow, $valor, $desconto, $frete);

                if ($valorImposto <= 0) {
                    if (!((bool) $ruleRow->cumulativo))
                        break;
                    continue;
                }

                $aplicadas[] = [
                    'aliquota' => round((float) $aliq, 4),
                    'valor' => round((float) $valorImposto, 2),
                ];

                // regra não cumulativa interrompe as demais
                if (!((bool) $ruleRow->cumulativo))
                    break;
            }

            if (!empty($aplicadas)) {
                $total = array_sum(array_column($aplicadas, 'valor'));

                $resultado[] = [
                    'imposto' => $taxCodigo,
                    'tax_id' => $meta->id,
                    'tax_nome' => $taxNome,
                    'linhas' => $aplicadas,
                    'total' => round($total, 2),
                ];
            }
        }
        // Totais gerais
        $totalImpostos = round(array_sum(array_map(fn($x) => $x['total'] ?? 0, $resultado)), 2);
        $resultado['_total_impostos'] = $totalImpostos;
        $resultado['_total_com_impostos'] = round($valor + $totalImpostos, 2);

        // JSON compacto (para salvar em estoques.impostos_json)
        // Ex.: {"pis":{"aliquota":0.65,"valor":0.91},"cofins":{"aliquota":3,"valor":4.19},"icms_st":{"retido":true,"valor":0.00}}
        $compacto = [];
        foreach ($resultado as $blk) {
            if (!is_array($blk) || empty($blk['imposto']))
                continue;

            $code = strtolower((string) $blk['imposto']); // 'PIS' -> 'pis'
            $aliq = count($blk['linhas']) ? (float) $blk['linhas'][0]['aliquota'] : 0.0;

            // Heurística simples p/ "retido" (ajuste conforme suas regras)
            $retido = function_exists('str_contains')
                ? str_contains($code, 'st')
                : (strpos($code, 'st') !== false);

            $item = ['valor' => (float) $blk['total']];
            if ($retido) {
                $item['retido'] = true;
            } else {
                $item['aliquota'] = $aliq;
            }
            $compacto[$code] = $item;
        }
        $resultado['_compact'] = $compacto;

        return $resultado;
    }

    protected function especificidade(TaxRule $r): int
    {
        $campos = ['segment_id', 'categoria_produto_id', 'ncm_padrao', 'uf_origem', 'uf_destino', 'canal', 'tipo_operacao'];
        $score = 0;
        foreach ($campos as $c)
            if (!is_null($r->{$c}))
                $score++;
        return $score;
    }

    protected function avaliarRegra(TaxRule $r, float $valor, float $desconto, float $frete): array
    {
        [$base] = $this->calcularBase(
            $r->base_formula,
            $valor,
            $desconto,
            $frete,
            (float) ($r->aliquota_percent ?? 0.0),
            $r->expression
        );

        $aliq = (float) ($r->aliquota_percent ?? 0.0);
        $fixo = (float) ($r->valor_fixo ?? 0.0);

        switch ($this->enumInt($r->metodo)) {
            case 1:
                $imposto = $base * ($aliq / 100.0);
                break; // percentual
            case 2:
                $imposto = $fixo;
                break;                    // valor fixo
            case 3:                                            // fórmula personalizada
                $vars = [
                    'valor' => $valor,
                    'desconto' => $desconto,
                    'frete' => $frete,
                    'aliquota' => $aliq,
                    'rate' => $aliq,
                    'base' => $base,
                    'imposto' => 0.0,
                ];
                [, $imposto] = $this->executarExpression($r->expression, $vars);
                break;
            default:
                $imposto = $base * ($aliq / 100.0);
                break;
        }

        return [(float) $base, (float) $imposto, (float) $aliq];
    }

    private function baseLabel(?string $f): string
    {
        return match ($f) {
            'valor' => 'Valor',
            'valor_menos_desc' => 'Valor - Desconto',
            'valor_mais_frete' => 'Valor - Desconto + Frete',
            'personalizada' => 'Personalizada (expression)',
            default => (string) $f,
        };
    }

    protected function calcularBase(?string $baseFormula, float $valor, float $desconto, float $frete, float $aliq, ?string $expr): array
    {
        $baseFormula = $baseFormula ?: 'valor_menos_desc';

        switch ($baseFormula) {
            case 'valor':
                return [$valor, false];
            case 'valor_mais_frete':
                return [max(0, $valor - $desconto + $frete), false];
            case 'personalizada':
                $vars = [
                    'valor' => $valor,
                    'desconto' => $desconto,
                    'frete' => $frete,
                    'aliquota' => $aliq,
                    'rate' => $aliq,
                    'base' => max(0, $valor - $desconto + $frete),
                    'imposto' => 0.0,
                ];
                [$base,] = $this->executarExpression($expr, $vars);
                return [$base, true];
            case 'valor_menos_desc':
            default:
                return [max(0, $valor - $desconto), false];
        }
    }

    protected function executarExpression(?string $expr, array $vars): array
    {
        if (!$expr || trim($expr) === '') {
            $imposto = $vars['base'] * ($vars['aliquota'] / 100.0);
            return [(float) $vars['base'], (float) $imposto];
        }
        $allowed = array_keys($vars);
        $lines = preg_split('/[;\r\n]+/', $expr);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || !str_contains($line, '='))
                continue;
            [$left, $right] = array_map('trim', explode('=', $line, 2));
            if (!in_array($left, $allowed, true))
                continue;

            $safe = $right;
            foreach ($allowed as $v) {
                $safe = preg_replace('/\b' . preg_quote($v, '/') . '\b/u', (string) ($vars[$v]), $safe);
            }
            if (preg_match('/[^0-9\.\+\-\*\/\(\)\s]/', $safe))
                continue;

            try {
                $result = eval ("return (float)($safe);");
                if (is_finite($result))
                    $vars[$left] = $result;
            } catch (\Throwable $e) {
            }
        }
        return [(float) $vars['base'], (float) $vars['imposto']];
    }

    private function enumInt($maybeEnum): ?int
    {
        if (is_object($maybeEnum) && property_exists($maybeEnum, 'value')) {
            return (int) $maybeEnum->value;
        }
        return is_numeric($maybeEnum) ? (int) $maybeEnum : null;
    }

    private function enumStr($maybeEnum): ?string
    {
        if (is_object($maybeEnum) && property_exists($maybeEnum, 'value')) {
            return (string) $maybeEnum->value;
        }
        return $maybeEnum !== null ? (string) $maybeEnum : null;
    }

    private function enumIntRow($v): ?int
    {
        return is_numeric($v) ? (int) $v : (is_null($v) ? null : null);
    }

    private function enumStrRow($v): ?string
    {
        return $v !== null ? (string) $v : null;
    }

    private function especificidadeRow($r): int
    {
        $campos = ['segment_id', 'categoria_produto_id', 'ncm_padrao', 'uf_origem', 'uf_destino', 'canal', 'tipo_operacao'];
        $score = 0;
        foreach ($campos as $c)
            if (!is_null($r->{$c}))
                $score++;
        return $score;
    }

    private function avaliarRegraFromRow($r, float $valor, float $desconto, float $frete): array
    {
        [$base] = $this->calcularBase(
            $r->base_formula,
            $valor,
            $desconto,
            $frete,
            (float) ($r->aliquota_percent ?? 0.0),
            $r->expression
        );

        $aliq = (float) ($r->aliquota_percent ?? 0.0);
        $fixo = (float) ($r->valor_fixo ?? 0.0);

        switch ($this->enumIntRow($r->metodo)) {
            case 1:
                $imposto = $base * ($aliq / 100.0);
                break; // percentual
            case 2:
                $imposto = $fixo;
                break;                    // valor fixo
            case 3:                                             // fórmula
                $vars = [
                    'valor' => $valor,
                    'desconto' => $desconto,
                    'frete' => $frete,
                    'aliquota' => $aliq,
                    'rate' => $aliq,
                    'base' => $base,
                    'imposto' => 0.0,
                ];
                [, $imposto] = $this->executarExpression($r->expression, $vars);
                break;
            default:
                $imposto = $base * ($aliq / 100.0);
                break;
        }

        return [(float) $base, (float) $imposto, (float) $aliq];
    }
}
