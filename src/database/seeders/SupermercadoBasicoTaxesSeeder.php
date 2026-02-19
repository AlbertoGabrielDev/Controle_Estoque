<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupermercadoBasicoTaxesSeeder extends Seeder
{
    /**
     * Categorias alvo no seu catálogo:
     * - "Carnes e Aves"
     * - "Hortifruti" (frutas/verduras)
     * - "Peixes e Frutos do Mar"
     *
     * Ajuste os nomes abaixo conforme estão na sua tabela `categorias`.
     */
    private array $categoriasAlvo = [
        'Carnes e Aves',
        'Hortifruti',
        'Peixes e Frutos do Mar',
    ];

    // Alíquotas exemplo (ajuste conforme necessário)
    private float $ICMS   = 19.00;
    private float $PIS    = 0.65;
    private float $COFINS = 3.00;

    public function run(): void
    {
        DB::transaction(function () {
            // 1) Garante impostos base
            $taxIds = [
                'ICMS'   => $this->ensureTax('ICMS',   'ICMS (próprio)'),
                'PIS'    => $this->ensureTax('PIS',    'PIS'),
                'COFINS' => $this->ensureTax('COFINS', 'COFINS'),
            ];

            // 2) Mapeia nomes -> IDs das categorias
            $categorias = $this->fetchCategorias($this->categoriasAlvo);

            // 3) Cria/atualiza regras por categoria (escopo ITEM)
            foreach ($categorias as $nome => $idCategoria) {
                if (!$idCategoria) {
                    $this->warn("Categoria '{$nome}' não encontrada; pulando.");
                    continue;
                }

                // ICMS (GO -> GO)
                $this->upsertRegraPercentual(
                    taxId: $taxIds['ICMS'],
                    categoriaId: $idCategoria,
                    ufOrigem: 'GO',
                    ufDestino: 'GO',
                    aliquota: $this->ICMS,
                    prioridade: 10,
                    cumulativo: false,
                    tipoOperacao: 'venda'
                );

                // PIS (GO -> GO)
                $this->upsertRegraPercentual(
                    taxId: $taxIds['PIS'],
                    categoriaId: $idCategoria,
                    ufOrigem: 'GO',
                    ufDestino: 'GO',
                    aliquota: $this->PIS,
                    prioridade: 20,
                    cumulativo: false,
                    tipoOperacao: 'venda'
                );

                // COFINS (GO -> GO)
                $this->upsertRegraPercentual(
                    taxId: $taxIds['COFINS'],
                    categoriaId: $idCategoria,
                    ufOrigem: 'GO',
                    ufDestino: 'GO',
                    aliquota: $this->COFINS,
                    prioridade: 30,
                    cumulativo: false,
                    tipoOperacao: 'venda'
                );
            }
        });
    }

    /**
     * Cria/atualiza um imposto e retorna seu ID.
     */
    private function ensureTax(string $codigo, string $nome): int
    {
        $row = DB::table('taxes')->where('codigo', $codigo)->first();

        if ($row) {
            DB::table('taxes')->where('id', $row->id)->update([
                'nome'       => $nome,
                'ativo'      => true,
                'updated_at' => now(),
            ]);
            return (int) $row->id;
        }

        return (int) DB::table('taxes')->insertGetId([
            'codigo'     => $codigo,
            'nome'       => $nome,
            'ativo'      => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Busca IDs das categorias pelo nome.
     * Retorna [ 'Nome' => id_categoria|null, ... ]
     */
    private function fetchCategorias(array $nomes): array
    {
        $out = array_fill_keys($nomes, null);

        $rows = DB::table('categorias')
            ->whereIn('nome_categoria', $nomes)
            ->get(['id_categoria', 'nome_categoria']);

        foreach ($rows as $r) {
            $out[$r->nome_categoria] = (int) $r->id_categoria;
        }

        return $out;
    }

    /**
     * Cria/atualiza regra percentual (escopo=Item) por categoria e UF.
     * - base_formula: valor_menos_desc (preço líquido, sem frete)
     * - metodo: 1 (percent)
     * - cumulativo: conforme parâmetro
     * - vigência: sem datas (sempre válida) — ajuste se precisar
     */
    private function upsertRegraPercentual(
        int $taxId,
        int $categoriaId,
        string $ufOrigem,
        string $ufDestino,
        float $aliquota,
        int $prioridade = 10,
        bool $cumulativo = false,
        string $tipoOperacao = 'venda'
    ): void {
        $exists = DB::table('tax_rules as tr')
            ->join('tax_rule_alvos as tra', 'tra.tax_rule_id', '=', 'tr.id')
            ->where('tr.tax_id', $taxId)
            ->where('tr.escopo', 1) // 1 = Item
            ->where('tra.id_categoria_fk', $categoriaId)
            ->where('tr.uf_origem', $ufOrigem)
            ->where('tr.uf_destino', $ufDestino)
            ->where('tr.tipo_operacao', $tipoOperacao)
            ->select('tr.id')
            ->first();

        $payload = [
            'segment_id'           => null,
            'ncm_padrao'           => null,
            'canal'                => null,
            'vigencia_inicio'      => null,
            'vigencia_fim'         => null,

            'aliquota_percent'     => number_format($aliquota, 4, '.', ''),
            'valor_fixo'           => null,
            'base_formula'         => 'valor_menos_desc',
            'expression'           => null,
            'metodo'               => 1, // 1=Percent

            'prioridade'           => $prioridade,
            'cumulativo'           => $cumulativo,
            'updated_at'           => now(),
        ];

        if ($exists) {
            DB::table('tax_rules')->where('id', $exists->id)->update($payload);
            DB::table('tax_rule_alvos')->updateOrInsert(
                ['tax_rule_id' => $exists->id, 'id_categoria_fk' => $categoriaId],
                ['updated_at' => now(), 'created_at' => now()]
            );
        } else {
            $payload = array_merge($payload, [
                'tax_id'        => $taxId,
                'escopo'        => 1,
                'uf_origem'     => $ufOrigem,
                'uf_destino'    => $ufDestino,
                'tipo_operacao' => $tipoOperacao,
                'created_at'    => now(),
            ]);
            $ruleId = (int) DB::table('tax_rules')->insertGetId($payload);
            DB::table('tax_rule_alvos')->insert([
                'tax_rule_id'     => $ruleId,
                'id_categoria_fk' => $categoriaId,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    private function warn(string $msg): void
    {
        // Apenas para log em seeder (não quebra execução)
        if (app()->runningInConsole()) {
            $this->command->warn($msg);
        }
    }
}
