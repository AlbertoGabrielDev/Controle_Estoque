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
        $catId = data_get($contexto, 'produto.categoria_id');
        $ncm = data_get($contexto, 'produto.ncm');
        $valor = (float) data_get($contexto, 'valores.valor', 0.0);
        $desconto = (float) data_get($contexto, 'valores.desconto', 0.0);
        $frete = (float) data_get($contexto, 'valores.frete', 0.0);
        $resultado = [];
        $taxes = Tax::query()
            ->where('ativo', true)
            ->with([
                'rules' => function ($q) use ($data) {
                    $q->vigentes($data);
                }
            ])
            ->get();

        foreach ($taxes as $tax) {
            $candidatas = $tax->rules->filter(function (TaxRule $r) use ($ignorarSegmento, $catId, $ncm, $ufO, $ufD, $canal, $tipoOp) {
                if ($this->enumInt($r->escopo) !== 1)
                    return false;
                if (!$ignorarSegmento) {
                    if ($r->segment_id !== null)
                        return false;
                }
                if (!is_null($r->categoria_produto_id) && (int) $r->categoria_produto_id !== (int) $catId)
                    return false;
                if (!is_null($r->ncm_padrao) && $r->ncm_padrao !== $ncm)
                    return false;
                $ufOrigemRegra = $this->enumStr($r->uf_origem);
                $ufDestinoRegra = $this->enumStr($r->uf_destino);
                if (!is_null($ufOrigemRegra) && $ufOrigemRegra !== $ufO)
                    return false;
                if (!is_null($ufDestinoRegra) && $ufDestinoRegra !== $ufD)
                    return false;

                if (!is_null($r->canal) && $r->canal !== $canal)
                    return false;
                if (!is_null($r->tipo_operacao) && $r->tipo_operacao !== $tipoOp)
                    return false;
                return true;
            });

            if ($candidatas->isEmpty())
                continue;

            $candidatas = $candidatas->sort(function (TaxRule $a, TaxRule $b) {
                $specA = $this->especificidade($a);
                $specB = $this->especificidade($b);
                if ($specA === $specB)
                    return $a->prioridade <=> $b->prioridade;
                return $specB <=> $specA;
            });

            $aplicadas = [];
            foreach ($candidatas as $rule) {

                [$base, $valorImposto, $aliq] = $this->avaliarRegra($rule, $valor, $desconto, $frete);
                if ($valorImposto <= 0) {
                    if (!$rule->cumulativo)
                        break;
                    continue;
                }
                $aplicadas[] = [
                    'tax' => $tax->codigo,
                    'tax_nome' => $tax->nome,
                    'rule_id' => $rule->id,
                    'aliquota' => $aliq,
                    'valor' => round($valorImposto, 2),
                    'base' => round($base, 2),
                    'cumulativo' => (bool) $rule->cumulativo,
                    'prioridade' => (int) $rule->prioridade,
                    'metodo' => $this->enumInt($rule->metodo),
                    'base_formula' => (string) $rule->base_formula,
                    'metodo_label' => match ($this->enumInt($rule->metodo)) {
                        1 => 'Percentual',
                        2 => 'Valor fixo',
                        3 => 'Fórmula',
                        default => 'Percentual',
                    },
                    'valor_fixo' => (float) ($rule->valor_fixo ?? 0),
                    'expression' => (string) ($rule->expression ?? ''),
                    'base_label' => $this->baseLabel($rule->base_formula),
                    'uf_origem' => $this->enumStr($rule->uf_origem),
                    'uf_destino' => $this->enumStr($rule->uf_destino),
                    'canal' => (string) ($rule->canal ?? ''),
                    'tipo_operacao' => (string) ($rule->tipo_operacao ?? ''),
                    'vigencia_inicio' => optional($rule->vigencia_inicio)->toDateString(),
                    'vigencia_fim' => optional($rule->vigencia_fim)->toDateString(),
                    'match' => [
                        'segment_id' => $rule->segment_id,
                        'categoria_produto_id' => $rule->categoria_produto_id,
                        'ncm_padrao' => $rule->ncm_padrao,
                    ],
                ];

                if (!$rule->cumulativo)
                    break;
            }

            if (!empty($aplicadas)) {
                $total = array_sum(array_column($aplicadas, 'valor'));
                $resultado[] = [
                    'imposto' => $tax->codigo,
                    'linhas' => $aplicadas,
                    'total' => round($total, 2),
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
        [$base, $baseFoiPersonalizada] = $this->calcularBase(
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
                break;
            case 2:
                $imposto = $fixo;
                break;
            case 3:
                $vars = [
                    'valor' => $valor,
                    'desconto' => $desconto,
                    'frete' => $frete,
                    'aliquota' => $aliq,
                    'rate' => $aliq,
                    'base' => $base,
                    'imposto' => 0.0,
                ];
                [$base, $imposto] = $this->executarExpression($r->expression, $vars);
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
                [$base, $imp] = $this->executarExpression($expr, $vars);
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
            } catch (\Throwable $e) { /* ignora */
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
}
