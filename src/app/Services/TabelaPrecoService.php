<?php

namespace App\Services;

use App\Models\TabelaPreco;
use App\Repositories\TabelaPrecoRepository;
use Illuminate\Support\Facades\DB;

class TabelaPrecoService
{
    public function __construct(private TabelaPrecoRepository $repo)
    {
    }

    public function create(array $data, array $itens = []): TabelaPreco
    {
        $tabela = $this->repo->create($data);
        $this->syncItens($tabela, $itens, $data['tipo_alvo'] ?? 'item');
        return $tabela;
    }

    public function update(TabelaPreco $tabelaPreco, array $data, array $itens = []): TabelaPreco
    {
        $tabela = $this->repo->update($tabelaPreco, $data);
        $this->syncItens($tabela, $itens, $data['tipo_alvo'] ?? 'item');
        return $tabela;
    }

    public function delete(TabelaPreco $tabelaPreco): void
    {
        $this->repo->delete($tabelaPreco);
    }

    private function syncItens(TabelaPreco $tabelaPreco, array $itens, string $tipoAlvo): void
    {
        $rows = [];
        $now = now();

        foreach ($itens as $item) {
            $rows[] = [
                'tabela_preco_id' => $tabelaPreco->id,
                'item_id' => $tipoAlvo === 'item' ? ($item['item_id'] ?? null) : null,
                'produto_id' => $tipoAlvo === 'produto' ? ($item['produto_id'] ?? null) : null,
                'marca_id' => $tipoAlvo === 'produto' ? ($item['marca_id'] ?? null) : null,
                'fornecedor_id' => $tipoAlvo === 'produto' ? ($item['fornecedor_id'] ?? null) : null,
                'preco' => $item['preco'] ?? 0,
                'desconto_percent' => $item['desconto_percent'] ?? 0,
                'quantidade_minima' => $item['quantidade_minima'] ?? 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('tabela_preco_itens')
            ->where('tabela_preco_id', $tabelaPreco->id)
            ->delete();

        $rows = array_values(array_filter($rows, function ($row) {
            return !empty($row['item_id']) || !empty($row['produto_id']);
        }));

        if ($rows) {
            DB::table('tabela_preco_itens')->insert($rows);
        }
    }
}
