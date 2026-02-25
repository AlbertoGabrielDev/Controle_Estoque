<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\ContaContabil;
use Modules\Finance\Repositories\ContaContabilRepository;

class ContaContabilService
{
    public function __construct(private ContaContabilRepository $repo)
    {
    }

    public function create(array $data): ContaContabil
    {
        return $this->repo->create($data);
    }

    public function update(ContaContabil $contaContabil, array $data): ContaContabil
    {
        return $this->repo->update($contaContabil, $data);
    }

    public function delete(ContaContabil $contaContabil): void
    {
        $this->repo->delete($contaContabil);
    }
}
