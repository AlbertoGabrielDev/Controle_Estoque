<?php

namespace App\Services;

use App\Models\TabelaPreco;
use App\Repositories\TabelaPrecoRepository;

class TabelaPrecoService
{
    public function __construct(private TabelaPrecoRepository $repo)
    {
    }

    public function create(array $data, array $itens = []): TabelaPreco
    {
        $tabela = $this->repo->create($data);
        $this->syncItens($tabela, $itens);
        return $tabela;
    }

    public function update(TabelaPreco $tabelaPreco, array $data, array $itens = []): TabelaPreco
    {
        $tabela = $this->repo->update($tabelaPreco, $data);
        $this->syncItens($tabela, $itens);
        return $tabela;
    }

    public function delete(TabelaPreco $tabelaPreco): void
    {
        $this->repo->delete($tabelaPreco);
    }

    private function syncItens(TabelaPreco $tabelaPreco, array $itens): void
    {
        $payload = [];

        foreach ($itens as $item) {
            if (!isset($item['item_id'])) {
                continue;
            }

            $payload[$item['item_id']] = [
                'preco' => $item['preco'] ?? 0,
                'desconto_percent' => $item['desconto_percent'] ?? 0,
                'quantidade_minima' => $item['quantidade_minima'] ?? 1,
            ];
        }

        $tabelaPreco->itens()->sync($payload);
    }
}
