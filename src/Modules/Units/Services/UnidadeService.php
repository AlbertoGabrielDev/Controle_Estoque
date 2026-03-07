<?php

namespace Modules\Units\Services;

use Modules\Units\Models\Unidades;
use Modules\Units\Repositories\UnidadesRepository;

class UnidadeService
{
    public function __construct(private UnidadesRepository $repo)
    {
    }
    public function create(array $data, ?int $userId): Unidades
    {
        return $this->repo->create([
            'nome' => (string) $data['nome'],
            'id_users_fk' => $userId,
        ]);
    }

    public function findOrFail(int $unidadeId): Unidades
    {
        return $this->repo->find($unidadeId);
    }

    public function update(int $unidadeId, array $data): Unidades
    {
        $unidade = $this->findOrFail($unidadeId);

        return $this->repo->update([
            'nome' => (string) $data['nome'],
        ], $unidadeId);
    }
}
