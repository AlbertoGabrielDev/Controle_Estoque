<?php

namespace Modules\Finance\Repositories;

use Modules\Finance\Models\ContaContabil;

class ContaContabilRepository
{
    public function __construct(private ContaContabil $model)
    {
    }

    public function create(array $data): ContaContabil
    {
        return $this->model->create($data);
    }

    public function update(ContaContabil $contaContabil, array $data): ContaContabil
    {
        $contaContabil->update($data);
        return $contaContabil;
    }

    public function delete(ContaContabil $contaContabil): void
    {
        $contaContabil->delete();
    }
}
