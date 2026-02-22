<?php

namespace App\Services;

use App\Models\Imposto;
use App\Repositories\ImpostoRepository;

class ImpostoService
{
    public function __construct(private ImpostoRepository $repo)
    {
    }

    public function create(array $data): Imposto
    {
        return $this->repo->create($data);
    }

    public function update(Imposto $imposto, array $data): Imposto
    {
        return $this->repo->update($imposto, $data);
    }

    public function delete(Imposto $imposto): void
    {
        $this->repo->delete($imposto);
    }
}
