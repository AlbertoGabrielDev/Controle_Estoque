<?php

namespace Modules\Units\Services;

use Modules\Units\Models\Unidades;

class UnidadeService
{
    public function create(array $data, ?int $userId): Unidades
    {
        return Unidades::query()->create([
            'nome' => (string) $data['nome'],
            'id_users_fk' => $userId,
        ]);
    }

    public function findOrFail(int $unidadeId): Unidades
    {
        return Unidades::query()->findOrFail($unidadeId);
    }

    public function update(int $unidadeId, array $data): Unidades
    {
        $unidade = $this->findOrFail($unidadeId);
        $unidade->update([
            'nome' => (string) $data['nome'],
        ]);

        return $unidade->refresh();
    }
}
