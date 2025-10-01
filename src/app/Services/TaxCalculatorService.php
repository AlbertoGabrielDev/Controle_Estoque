<?php

namespace App\Services\Tax;

use App\Models\{Tax, TaxRule};

class TaxCalculatorService
{
    /**
     * Calcula impostos para um contexto.
     *
     * @param array $contexto
     * [
     *   'data' => 'YYYY-MM-DD',
     *   'cliente' => ['segment_id'=>int|null, 'uf'=> 'SP'|'RJ'|... ],
     *   'operacao' => ['tipo'=>'venda'|'devolucao', 'canal'=>'loja'|'ecommerce'|..., 'uf_origem'=>'SP', 'uf_destino'=>'RJ'],
     *   'produto' => ['categoria_id'=>int|null, 'ncm'=>string|null],
     *   'valores' => ['valor'=>float, 'desconto'=>float, 'frete'=>float]
     * ]
     *
     * @return array breakdown por imposto + _total_impostos
     */
    public function calcular(array $contexto): array
    {
        $data     = data_get($contexto, 'data') ?: now()->toDateString();
        $segment  = data_get($contexto, 'cliente.segment_id');
        $ufCli    = data_get($contexto, 'cliente.uf');
        $tipoOp   = data_get($contexto, 'operacao.tipo', 'venda');
        $canal    = data_get($contexto, 'operacao.canal');
        $ufO      = data_get($contexto, 'operacao.uf_origem');
        $ufD      = data_get($contexto, 'operacao.uf_destino', $ufCli);
        $catId    = data_get($contexto, 'produto.categoria_id');
        $ncm      = data_get($contexto, 'produto.ncm');

        $valor    = (float) data_get($contexto, 'valores.valor', 0.0);
        $desconto = (float) data_get($contexto, 'valores.desconto', 0.0);
        $frete    = (float) data_get($contexto, 'valores.frete', 0.0);

        $resultado = [];
        $taxes = Tax::query()
            ->where('ativo', true)
            ->with(['rules' => fn($q) => $q->vigentes($data)])
            ->get();

        foreach ($taxes as $tax) {
            $candidatas = $tax->rules->filter(function (TaxRule $r) use ($segment, $catId, $ncm, $ufO, $ufD, $canal, $tipoOp) {
                if (!is_null($r->segment_id) && $r->segment_id != $segment) return false;
                if (!is_null($r->categoria_produto_id) && $r->categoria_produto_id != $catId) return false;
                if (!is_null($r->ncm_padrao) && $r->ncm_padrao !== $ncm) return false;
                if (!is_null($r->uf_origem) && $r->uf_origem !== $ufO) return false;
                if (!is_null($r->uf_destino) && $r->uf_destino !== $ufD) return false;
                if (!is_null($r->canal) && $r->canal !== $canal) return false;
                if (!is_null($r->tipo_operacao) && $r->tipo_operacao !== $tipoOp) return false;
                return true;
            });

            if ($candidatas->isEmpty()) continue;

            // Ordena por maior especificidade (menos nulls) e menor prioridade
            $candidatas = $candidatas->sort(function(TaxRule $a, TaxRule $b) {
                $specA = $this->especificidade($a);
                $specB = $this->especificidade($b);
                if ($specA === $specB) {
                    return $a->prioridade <=> $b->prioridade;
                }
                return $specB <=> $specA; // mais específico primeiro
            });

            $aplicadas = [];
            foreach ($candidatas as $rule) {
                [$base, $valorImposto, $aliquota] = $this->avaliarRegra($rule, $valor, $desconto, $frete);
                if ($valorImposto <= 0) continue;

                $aplicadas[] = [
                    'tax'        => $tax->codigo,
                    'tax_nome'   => $tax->nome,
                    'rule_id'    => $rule->id,
                    'aliquota'   => $aliquota,
                    'base'       => round($base, 2),
                    'valor'      => round($valorImposto, 2),
                    'cumulativo' => $rule->cumulativo,
                    'prioridade' => $rule->prioridade,
                ];

                if (!$rule->cumulativo) break; // primeira regra vencedora para esse imposto
            }

            if (!empty($aplicadas)) {
                $total = array_sum(array_column($aplicadas, 'valor'));
                $resultado[] = [
                    'imposto' => $tax->codigo,
                    'linhas'  => $aplicadas,
                    'total'   => round($total, 2),
                ];
            }
        }

        $resultado['_total_impostos'] = round(array_sum(array_map(fn($x)=> $x['total'] ?? 0, $resultado)), 2);
        return $resultado;
    }

    protected function especificidade(TaxRule $r): int
    {
        $campos = ['segment_id','categoria_produto_id','ncm_padrao','uf_origem','uf_destino','canal','tipo_operacao'];
        $score = 0;
        foreach ($campos as $c) {
            if (!is_null($r->{$c})) $score++;
        }
        return $score;
    }

    protected function avaliarRegra(TaxRule $r, float $valor, float $desconto, float $frete): array
    {
        $aliquota = (float) ($r->aliquota_percent ?? 0);
        switch ($r->base_formula) {
            case 'valor':
                $base = $valor;
                break;
            case 'valor_mais_frete':
                $base = $valor + $frete;
                break;
            case 'personalizada':
                $base = $valor - $desconto;
                $vars = [
                    'valor'    => $valor,
                    'desconto' => $desconto,
                    'frete'    => $frete,
                    'aliquota' => $aliquota,
                    'base'     => $base,
                    'imposto'  => 0.0,
                ];
                [$base, $imposto] = $this->executarExpression($r->expression, $vars);
                return [(float)$base, (float)$imposto, $aliquota];
            case 'valor_menos_desc':
            default:
                $base = max(0, $valor - $desconto);
                break;
        }

        $valorImposto = $base * ($aliquota/100.0);
        return [$base, $valorImposto, $aliquota];
    }

    protected function executarExpression(?string $expr, array $vars): array
    {
        if (!$expr) {
            $base = $vars['valor'] - $vars['desconto'] + $vars['frete'];
            $imposto = $base * ($vars['aliquota']/100.0);
            return [$base, $imposto];
        }

        $allowedVars = array_keys($vars);
        $lines = preg_split('/[;\r\n]+/', $expr);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || !str_contains($line, '=')) continue;

            [$left, $right] = array_map('trim', explode('=', $line, 2));
            if (!in_array($left, $allowedVars, true)) continue;

            $safe = $right;
            foreach ($allowedVars as $v) {
                $safe = preg_replace('/\b'.preg_quote($v, '/').'\b/', (string)($vars[$v]), $safe);
            }
            if (preg_match('/[^0-9\.\+\-\*\/\(\)\s]/', $safe)) continue;

            try {
                // phpcs:ignore
                $result = eval("return (float)($safe);");
                if (is_finite($result)) $vars[$left] = $result;
            } catch (\Throwable $e) { /* ignora */ }
        }

        return [$vars['base'], $vars['imposto']];
    }
}
