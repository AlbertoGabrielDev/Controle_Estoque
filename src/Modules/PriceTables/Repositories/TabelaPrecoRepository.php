<?php

namespace Modules\PriceTables\Repositories;

use Modules\PriceTables\Models\TabelaPreco;

interface TabelaPrecoRepository
{
    public function create(array $data): TabelaPreco;

    public function update(TabelaPreco $tabelaPreco, array $data): TabelaPreco;

    public function delete(TabelaPreco $tabelaPreco): void;
}
