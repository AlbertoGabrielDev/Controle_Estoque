<?php

namespace Modules\Taxes\Services;

use Modules\Taxes\Models\TaxRule;

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
        $segmentoCliente = data_get(
            $contexto,
            'clientes.segment_id',
            data_get(
                $contexto,
                'cliente.segment_id',
                data_get(
                    $contexto,
                    'customer.segment_id',
                    data_get($contexto, 'customer_segment_id')
                )
            )
        );
        $segmentoCliente = is_numeric($segmentoCliente) ? (int) $segmentoCliente : null;
        $escoposPermitidos = collect(data_get($contexto, 'escopos', [1, 2, 3]))
            ->map(fn($v) => is_numeric($v) ? (int) $v : null)
            ->filter(fn($v) => in_array($v, [1, 2, 3], true))
            ->values()
            ->all();
        if (empty($escoposPermitidos)) {
            $escoposPermitidos = [1, 2, 3];
        }

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
                't.id as tax_row_id',
                't.codigo as tax_codigo',
                't.nome as tax_nome',
                't.ativo',
                'tr.*',
                'm._match_score',
            ])
            ->get();
              
        // Agrupa por imposto
        $porImposto = $linhas->groupBy('tax_row_id');

        $resultado = [];
        $appliedRuleIds = [];
        $appliedTaxIds = [];

        // Para cada IMPOSTO, filtra as regras pelo contexto e aplica
        foreach ($porImposto as $taxId => $regras) {
         
            $meta = $regras->first();
            $taxCodigo = $meta->tax_codigo;
            $taxNome = $meta->tax_nome;
            
            // 1) Filtro contextual (escopo, UF, canal, NCM etc.)
            $candidatas = $regras->filter(fn($r) => $this->matchesContext(
                $r,
                ignorarSegmento: $ignorarSegmento,
                segmentoCliente: $segmentoCliente,
                ncm: $ncm,
                ufOrigem: $ufO,
                ufDestino: $ufD,
                canal: $canal,
                tipoOperacao: $tipoOp,
                escoposPermitidos: $escoposPermitidos,
            ));
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
                $linha = $this->avaliarRegraDetalhadaFromRow($ruleRow, $valor, $desconto, $frete);
                $valorImposto = (float) ($linha['valor'] ?? 0);

                if ($valorImposto <= 0) {
                    if (!((bool) $ruleRow->cumulativo))
                        break;
                    continue;
                }

                $aplicadas[] = $linha;
                if (!empty($linha['rule_id'])) {
                    $appliedRuleIds[] = (int) $linha['rule_id'];
                }
                if (!empty($linha['tax_id'])) {
                    $appliedTaxIds[] = (int) $linha['tax_id'];
                }

                // regra não cumulativa interrompe as demais
                if (!((bool) $ruleRow->cumulativo))
                    break;
            }

            if (!empty($aplicadas)) {
                $total = array_sum(array_column($aplicadas, 'valor'));

                $resultado[] = [
                    'imposto' => $taxCodigo,
                    'tax_id' => (int) ($meta->tax_row_id ?? $taxId),
                    'tax_nome' => $taxNome,
                    'rule_ids' => array_values(array_unique(array_map(
                        fn($l) => (int) ($l['rule_id'] ?? 0),
                        $aplicadas
                    ))),
                    'linhas' => $aplicadas,
                    'total' => round($total, 2),
                ];
            }
        }
        // Totais gerais
        $baseLiquida = max(0, $valor - $desconto) + $frete;
        $totalImpostos = round(array_sum(array_map(fn($x) => $x['total'] ?? 0, $resultado)), 2);
        $resultado['_total_impostos'] = $totalImpostos;
        $resultado['_total_sem_impostos'] = round($baseLiquida, 2);
        $resultado['_total_com_impostos'] = round($baseLiquida + $totalImpostos, 2);
        $resultado['_applied_rule_ids'] = array_values(array_unique($appliedRuleIds));
        $resultado['_applied_tax_ids'] = array_values(array_unique($appliedTaxIds));

        // JSON compacto (para salvar em estoques.impostos_json)
        // Ex.: {"pis":{"aliquota":0.65,"valor":0.91},"cofins":{"aliquota":3,"valor":4.19},"icms_st":{"retido":true,"valor":0.00}}
        $compacto = [];
        foreach ($resultado as $blk) {
            if (!is_array($blk) || empty($blk['imposto']))
                continue;

            $code = strtolower((string) $blk['imposto']); // 'PIS' -> 'pis'
            $aliq = count($blk['linhas']) ? (float) $blk['linhas'][0]['aliquota'] : 0.0;

            // Heurística conservadora para "retido":
            // evita falsos positivos em códigos internacionais como GST / HST.
            $retido = (bool) preg_match('/(?:^|[_\\-])st$/i', $code);

            $item = ['valor' => (float) $blk['total']];
            $firstLine = (is_array($blk['linhas'] ?? null) && !empty($blk['linhas'])) ? $blk['linhas'][0] : null;
            if (is_array($firstLine)) {
                $item['metodo'] = (int) ($firstLine['metodo'] ?? 1);
                if (!empty($firstLine['rule_id'])) {
                    $item['rule_id'] = (int) $firstLine['rule_id'];
                }
            }
            if (!empty($blk['rule_ids']) && is_array($blk['rule_ids'])) {
                $item['rule_ids'] = array_values(array_filter(array_map('intval', $blk['rule_ids'])));
            }
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

    private function matchesContext(
        $r,
        bool $ignorarSegmento,
        ?int $segmentoCliente,
        ?string $ncm,
        ?string $ufOrigem,
        ?string $ufDestino,
        ?string $canal,
        ?string $tipoOperacao,
        array $escoposPermitidos
    ): bool {
        $escopo = (int) ($r->escopo ?? 1);
        if (!in_array($escopo, $escoposPermitidos, true)) {
            return false;
        }

        $segmentoRegra = is_numeric($r->segment_id ?? null) ? (int) $r->segment_id : null;
        if (!$ignorarSegmento && $segmentoRegra !== null && $segmentoRegra !== $segmentoCliente) {
            return false;
        }

        $ncmRegra = $this->normalizeNullableString($r->ncm_padrao ?? null);
        $ncmCtx = $this->normalizeNullableString($ncm);
        if ($ncmRegra !== null && $ncmRegra !== $ncmCtx) {
            return false;
        }

        $ufOriRegra = $this->normalizeNullableString($r->uf_origem ?? null, true);
        $ufDesRegra = $this->normalizeNullableString($r->uf_destino ?? null, true);
        $ufOriCtx = $this->normalizeNullableString($ufOrigem, true);
        $ufDesCtx = $this->normalizeNullableString($ufDestino, true);
        if ($ufOriRegra !== null && $ufOriRegra !== $ufOriCtx) {
            return false;
        }
        if ($ufDesRegra !== null && $ufDesRegra !== $ufDesCtx) {
            return false;
        }

        $canalRegra = $this->normalizeNullableString($r->canal ?? null);
        $canalCtx = $this->normalizeNullableString($canal);
        if ($canalRegra !== null && $canalRegra !== $canalCtx) {
            return false;
        }

        $tipoRegra = $this->normalizeNullableString($r->tipo_operacao ?? null);
        $tipoCtx = $this->normalizeNullableString($tipoOperacao);
        if ($tipoRegra !== null && $tipoRegra !== $tipoCtx) {
            return false;
        }

        return true;
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

    private function methodLabel(int $metodo): string
    {
        return match ($metodo) {
            1 => 'Percentual',
            2 => 'Valor fixo',
            3 => 'Fórmula',
            default => 'Desconhecido',
        };
    }

    private function scopeLabel(int $escopo): string
    {
        return match ($escopo) {
            1 => 'Item',
            2 => 'Frete',
            3 => 'Pedido',
            default => (string) $escopo,
        };
    }

    private function normalizeNullableString($value, bool $upper = false): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        return $upper ? strtoupper($value) : strtolower($value);
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

    public function validateFormulaDefinition(?string $expr): ?string
    {
        if ($expr === null || trim($expr) === '') {
            return null;
        }

        $vars = [
            'valor' => 100.0,
            'desconto' => 5.0,
            'frete' => 10.0,
            'aliquota' => 18.0,
            'rate' => 18.0,
            'base' => 95.0,
            'imposto' => 0.0,
        ];

        try {
            $this->executarExpression($expr, $vars, true);
            return null;
        } catch (\Throwable $e) {
            return 'Fórmula inválida: ' . $e->getMessage();
        }
    }

    protected function executarExpression(?string $expr, array $vars, bool $strict = false): array
    {
        if (!$expr || trim($expr) === '') {
            $imposto = $vars['base'] * ($vars['aliquota'] / 100.0);
            return [(float) $vars['base'], (float) $imposto];
        }

        $allowed = array_keys($vars);
        $lines = preg_split('/[;\r\n]+/', $expr);
        $processedAssignments = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '')
                continue;

            if (!str_contains($line, '=')) {
                if ($strict) {
                    throw new \InvalidArgumentException('Use atribuições no formato variavel = expressao.');
                }
                continue;
            }

            [$left, $right] = array_map('trim', explode('=', $line, 2));
            if (!in_array($left, $allowed, true)) {
                if ($strict) {
                    throw new \InvalidArgumentException("Variável '{$left}' não é permitida.");
                }
                continue;
            }

            try {
                $result = $this->evaluateArithmeticExpression($right, $vars);
                if (!is_finite($result)) {
                    throw new \RuntimeException('Resultado não finito.');
                }
                $vars[$left] = (float) $result;
                $processedAssignments++;
            } catch (\Throwable $e) {
                if ($strict) {
                    throw $e;
                }
                report($e);
                continue;
            }
        }

        if ($strict && $processedAssignments === 0) {
            throw new \InvalidArgumentException('A fórmula não possui nenhuma atribuição válida.');
        }

        return [(float) $vars['base'], (float) $vars['imposto']];
    }

    private function evaluateArithmeticExpression(string $expr, array $vars): float
    {
        $cursor = 0;
        $length = strlen($expr);
        $value = $this->parseAddSub($expr, $cursor, $length, $vars);
        $this->skipWs($expr, $cursor, $length);

        if ($cursor < $length) {
            throw new \InvalidArgumentException('Expressão contém tokens inválidos.');
        }

        return (float) $value;
    }

    private function parseAddSub(string $expr, int &$cursor, int $length, array $vars): float
    {
        $value = $this->parseMulDiv($expr, $cursor, $length, $vars);

        while (true) {
            $this->skipWs($expr, $cursor, $length);
            if ($cursor >= $length) {
                break;
            }

            $op = $expr[$cursor];
            if ($op !== '+' && $op !== '-') {
                break;
            }

            $cursor++;
            $rhs = $this->parseMulDiv($expr, $cursor, $length, $vars);
            $value = $op === '+' ? $value + $rhs : $value - $rhs;
        }

        return (float) $value;
    }

    private function parseMulDiv(string $expr, int &$cursor, int $length, array $vars): float
    {
        $value = $this->parseUnary($expr, $cursor, $length, $vars);

        while (true) {
            $this->skipWs($expr, $cursor, $length);
            if ($cursor >= $length) {
                break;
            }

            $op = $expr[$cursor];
            if ($op !== '*' && $op !== '/') {
                break;
            }

            $cursor++;
            $rhs = $this->parseUnary($expr, $cursor, $length, $vars);
            if ($op === '/') {
                if (abs($rhs) < 1e-12) {
                    throw new \InvalidArgumentException('Divisão por zero.');
                }
                $value /= $rhs;
            } else {
                $value *= $rhs;
            }
        }

        return (float) $value;
    }

    private function parseUnary(string $expr, int &$cursor, int $length, array $vars): float
    {
        $this->skipWs($expr, $cursor, $length);
        if ($cursor < $length && ($expr[$cursor] === '+' || $expr[$cursor] === '-')) {
            $op = $expr[$cursor];
            $cursor++;
            $value = $this->parseUnary($expr, $cursor, $length, $vars);
            return $op === '-' ? -$value : $value;
        }

        return $this->parsePrimary($expr, $cursor, $length, $vars);
    }

    private function parsePrimary(string $expr, int &$cursor, int $length, array $vars): float
    {
        $this->skipWs($expr, $cursor, $length);
        if ($cursor >= $length) {
            throw new \InvalidArgumentException('Expressão incompleta.');
        }

        $char = $expr[$cursor];
        if ($char === '(') {
            $cursor++;
            $value = $this->parseAddSub($expr, $cursor, $length, $vars);
            $this->skipWs($expr, $cursor, $length);
            if ($cursor >= $length || $expr[$cursor] !== ')') {
                throw new \InvalidArgumentException('Parêntese não fechado.');
            }
            $cursor++;
            return (float) $value;
        }

        if (ctype_digit($char) || $char === '.') {
            return $this->readNumber($expr, $cursor, $length);
        }

        if (ctype_alpha($char) || $char === '_') {
            $identifier = $this->readIdentifier($expr, $cursor, $length);
            if (!array_key_exists($identifier, $vars)) {
                throw new \InvalidArgumentException("Variável '{$identifier}' não é permitida.");
            }
            return (float) ($vars[$identifier] ?? 0);
        }

        throw new \InvalidArgumentException('Token inválido na expressão.');
    }

    private function readNumber(string $expr, int &$cursor, int $length): float
    {
        $start = $cursor;
        $hasDot = false;

        while ($cursor < $length) {
            $char = $expr[$cursor];
            if (ctype_digit($char)) {
                $cursor++;
                continue;
            }
            if ($char === '.' && !$hasDot) {
                $hasDot = true;
                $cursor++;
                continue;
            }
            break;
        }

        $literal = substr($expr, $start, $cursor - $start);
        if ($literal === '' || $literal === '.') {
            throw new \InvalidArgumentException('Número inválido.');
        }

        return (float) $literal;
    }

    private function readIdentifier(string $expr, int &$cursor, int $length): string
    {
        $start = $cursor;
        while ($cursor < $length) {
            $char = $expr[$cursor];
            if (ctype_alnum($char) || $char === '_') {
                $cursor++;
                continue;
            }
            break;
        }

        return substr($expr, $start, $cursor - $start);
    }

    private function skipWs(string $expr, int &$cursor, int $length): void
    {
        while ($cursor < $length && ctype_space($expr[$cursor])) {
            $cursor++;
        }
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

    private function avaliarRegraDetalhadaFromRow($r, float $valor, float $desconto, float $frete): array
    {
        [$base, $imposto, $aliq] = $this->avaliarRegraFromRow($r, $valor, $desconto, $frete);

        $metodo = $this->enumIntRow($r->metodo) ?? 1;
        $escopo = is_numeric($r->escopo ?? null) ? (int) $r->escopo : 1;
        $ruleId = (int) ($r->id ?? 0);
        $taxId = (int) ($r->tax_row_id ?? $r->tax_id ?? 0);

        return [
            'rule_id' => $ruleId > 0 ? $ruleId : null,
            'tax_id' => $taxId > 0 ? $taxId : null,
            'tax_code' => (string) ($r->tax_codigo ?? $r->codigo ?? ''),
            'tax_nome' => (string) ($r->tax_nome ?? $r->nome ?? ''),
            'escopo' => $escopo,
            'escopo_label' => $this->scopeLabel($escopo),
            'metodo' => $metodo,
            'metodo_label' => $this->methodLabel($metodo),
            'base_formula' => (string) ($r->base_formula ?? ''),
            'base_formula_label' => $this->baseLabel($r->base_formula ?? null),
            'base' => round((float) $base, 4),
            'aliquota' => round((float) $aliq, 4),
            'valor_fixo' => round((float) ($r->valor_fixo ?? 0), 2),
            'valor' => round((float) $imposto, 2),
            'prioridade' => (int) ($r->prioridade ?? 0),
            'cumulativo' => (bool) ($r->cumulativo ?? false),
            '_match_score' => (int) ($r->_match_score ?? 0),
            // Compatibilidade com consumidores antigos que buscavam rule_dump.id
            'rule_dump' => ['id' => $ruleId > 0 ? $ruleId : null],
        ];
    }
}
