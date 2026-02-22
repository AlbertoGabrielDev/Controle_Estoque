<?php

namespace App\Repositories;

use App\Models\Imposto;

class ImpostoRepository
{
    public function __construct(private Imposto $model)
    {
    }

    public function create(array $data): Imposto
    {
        return $this->model->create($data);
    }

    public function update(Imposto $imposto, array $data): Imposto
    {
        $imposto->update($data);
        return $imposto;
    }

    public function delete(Imposto $imposto): void
    {
        $imposto->delete();
    }
}
