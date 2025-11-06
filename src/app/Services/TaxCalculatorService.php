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
        $ufCli = data_get($contexto, 'clientes.uf');
        $tipoOp = data_get($contexto, 'operacao.tipo', 'venda');
        $canal = data_get($contexto, 'operacao.canal');
        $ufO = data_get($contexto, 'operacao.uf_origem');
        $ufD = data_get($contexto, 'operacao.uf_destino', $ufCli);
        $catId = data_get($contexto, 'produto.categoria_id'); // <- agora vem da pivot
        $ncm = data_get($contexto, 'produto.ncm');
        $valor = (float) data_get($contexto, 'valores.valor', 0.0);
        $desconto = (float) data_get($contexto, 'valores.desconto', 0.0);
        $frete = (float) data_get($contexto, 'valores.frete', 0.0);
       
        $linhas = \DB::table('tax_rules as tr')
            ->join('taxes as t', 't.id', '=', 'tr.tax_id')
            ->select([
                't.id as tax_id',
                't.codigo',
                't.nome',
                't.ativo',
                'tr.*',
            ])
            ->where('t.ativo', true)
            ->when($catId, function ($q) use ($catId) {
                $q->where(function ($qq) use ($catId) {
                    $qq->whereNull('tr.categoria_produto_id')
                        ->orWhere('tr.categoria_produto_id', $catId);
                });
            })
            ->where(function ($q) use ($data) {
                $q->whereNull('tr.vigencia_inicio')
                    ->orWhere('tr.vigencia_inicio', '<=', $data);
            })
            ->where(function ($q) use ($data) {
                $q->whereNull('tr.vigencia_fim')
                    ->orWhere('tr.vigencia_fim', '>=', $data);
            })
            ->get();

        $taxes = $linhas->groupBy('tax_id');
            
        $resultado = [];
        $totalImpostos = round(array_sum(array_map(fn($x) => $x['total'] ?? 0, $resultado)), 2); 
        //Facer o tax_rule aceitar varias categorias e o estoque vai receber o id da tax_rule
        $compact = [];
        foreach ($taxes as $taxId => $regrasDoImposto) {
            $meta = $regrasDoImposto->first();
            $taxCodigo = $meta->codigo;
            $taxNome = $meta->nome;

            $candidatas = $regrasDoImposto->filter(function ($r) use ($ignorarSegmento, $catId, $ncm, $ufO, $ufD, $canal, $tipoOp) {
                // Escopo (1 = venda/saída)
                if ($this->enumIntRow($r->escopo) !== 1)
                    return false;

                if (!$ignorarSegmento && $r->segment_id !== null)
                    return false;

                if (!is_null($r->categoria_produto_id) && (int) $r->categoria_produto_id !== (int) $catId)
                    return false;
                // if (!is_null($r->ncm_padrao) && (string) $r->ncm_padrao !== (string) $ncm)
                //     return false;

                $ufOriRegra = $this->enumStrRow($r->uf_origem);
                $ufDesRegra = $this->enumStrRow($r->uf_destino);
                if (!is_null($ufOriRegra) && $ufOriRegra !== $ufO)
                    return false;
                if (!is_null($ufDesRegra) && $ufDesRegra !== $ufD)
                    return false;

                // Canal / Tipo operação
                // if (!is_null($r->canal) && (string) $r->canal !== (string) $canal)
                //     return false;
                // if (!is_null($r->tipo_operacao) && (string) $r->tipo_operacao !== (string) $tipoOp)
                //     return false;

                return true;
            });
       
            if ($candidatas->isEmpty()) {
                continue;
            }

            $candidatas = $candidatas->sort(function ($a, $b) {
                $sa = $this->especificidadeRow($a);
                $sb = $this->especificidadeRow($b);
                if ($sa === $sb) {
                    return ((int) $a->prioridade) <=> ((int) $b->prioridade);
                }
                return $sb <=> $sa;
            });

            $aplicadas = [];
            foreach ($candidatas as $ruleRow) {
                [$base, $valorImposto, $aliq] = $this->avaliarRegraFromRow($ruleRow, $valor, $desconto, $frete);

                if ($valorImposto <= 0) {
                    if (!((bool) $ruleRow->cumulativo))
                        break;
                    continue;
                }

                $ruleDump = [
                    'id' => (int) $ruleRow->id,
                    'tax_id' => (int) $ruleRow->tax_id,
                ];

                $aplicadas[] = [
                    'tax' => $taxCodigo,
                    'tax_nome' => $taxNome,
                    'rule_id' => (int) $ruleRow->id,
                    'aliquota' => round((float) $aliq, 4),
                    'valor' => round((float) $valorImposto, 2),
                ];

                if (!((bool) $ruleRow->cumulativo))
                    break;
            }

            if (!empty($aplicadas)) {
                $total = array_sum(array_column($aplicadas, 'valor'));
                $resultado[] = [
                    'imposto' => $taxCodigo,
                    'tax_nome' => $taxNome,
                    'linhas' => $aplicadas,
                    'total' => round($total, 2),
                    'codigo' => $taxCodigo,   
                    'nome' => $taxNome, 

                ];
            }
        }

        $totalImpostos = round(array_sum(array_map(fn($x) => $x['total'] ?? 0, $resultado)), 2);
        $resultado['_total_impostos'] = $totalImpostos;
        $resultado['_total_com_impostos'] = round($valor + $totalImpostos, 2);

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
