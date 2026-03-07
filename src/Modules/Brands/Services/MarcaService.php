<?php

namespace Modules\Brands\Services;

use Modules\Brands\Models\Marca;
use Modules\Brands\Repositories\MarcaRepository;

class MarcaService
{
    public function __construct(private MarcaRepository $repository)
    {
    }

    public function findOrFail(int $marcaId): Marca
    {
        return $this->repository->find($marcaId);
    }

    public function create(array $data, ?int $userId): Marca
    {
        return $this->repository->create([
            'nome_marca' => (string) ($data['nome_marca'] ?? ''),
            'id_users_fk' => $userId,
        ]);
    }

    public function update(int $marcaId, array $data): Marca
    {
        $marca = $this->findOrFail($marcaId);

        return $this->repository->update([
            'nome_marca' => (string) ($data['nome_marca'] ?? $marca->nome_marca),
        ], $marcaId);
    }
}
