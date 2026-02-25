<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\Despesa;
use Modules\Finance\Repositories\DespesaRepository;

class DespesaService
{
    public function __construct(private DespesaRepository $repo)
    {
    }

    public function create(array $data): Despesa
    {
        return $this->repo->create($data);
    }

    public function update(Despesa $despesa, array $data): Despesa
    {
        return $this->repo->update($despesa, $data);
    }

    public function delete(Despesa $despesa): void
    {
        $this->repo->delete($despesa);
    }
}
