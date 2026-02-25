<?php

namespace Modules\Brands\Services;

use Modules\Brands\Models\Marca;

class MarcaService
{
    public function findOrFail(int $marcaId): Marca
    {
        return Marca::query()->findOrFail($marcaId);
    }

    public function create(array $data, ?int $userId): Marca
    {
        return Marca::query()->create([
            'nome_marca' => (string) ($data['nome_marca'] ?? ''),
            'id_users_fk' => $userId,
        ]);
    }

    public function update(int $marcaId, array $data): Marca
    {
        $marca = $this->findOrFail($marcaId);
        $marca->update([
            'nome_marca' => (string) ($data['nome_marca'] ?? $marca->nome_marca),
        ]);

        return $marca;
    }
}
