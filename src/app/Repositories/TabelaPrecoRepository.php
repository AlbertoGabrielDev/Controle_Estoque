<?php

namespace App\Repositories;

use App\Models\TabelaPreco;

class TabelaPrecoRepository
{
    public function __construct(private TabelaPreco $model)
    {
    }

    public function create(array $data): TabelaPreco
    {
        return $this->model->create($data);
    }

    public function update(TabelaPreco $tabelaPreco, array $data): TabelaPreco
    {
        $tabelaPreco->update($data);
        return $tabelaPreco;
    }

    public function delete(TabelaPreco $tabelaPreco): void
    {
        $tabelaPreco->delete();
    }
}
