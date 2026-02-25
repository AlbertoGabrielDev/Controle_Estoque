<?php

namespace Modules\Categories\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\Categories\Models\Categoria;
use RuntimeException;

class CategoriaService
{
    public function listForHome(bool $canViewInactive): Collection
    {
        $query = Categoria::query()->withCount('produtos');

        if (!$canViewInactive) {
            $query->where('ativo', 1);
        }

        return $query
            ->orderBy('nome_categoria')
            ->get(['id_categoria', 'nome_categoria', 'imagem']);
    }

    public function listParentOptions(?int $exceptCategoriaId = null): Collection
    {
        $query = Categoria::query()
            ->select('id_categoria', 'nome_categoria')
            ->orderBy('nome_categoria');

        if ($exceptCategoriaId) {
            $query->where('id_categoria', '<>', $exceptCategoriaId);
        }

        return $query->get();
    }

    public function findOrFail(int $categoriaId): Categoria
    {
        return Categoria::query()->findOrFail($categoriaId);
    }

    public function create(array $validated, ?UploadedFile $imagem, ?int $userId): Categoria
    {
        return Categoria::query()->create([
            'codigo' => $validated['codigo'],
            'nome_categoria' => $validated['nome_categoria'],
            'tipo' => $validated['tipo'],
            'categoria_pai_id' => $validated['categoria_pai_id'] ?? null,
            'id_users_fk' => $userId,
            'imagem' => $this->storeImage($imagem),
            'ativo' => (bool) $validated['ativo'],
        ]);
    }

    public function update(int $categoriaId, array $validated, ?UploadedFile $imagem): Categoria
    {
        $categoria = $this->findOrFail($categoriaId);

        $data = [
            'codigo' => $validated['codigo'],
            'nome_categoria' => $validated['nome_categoria'],
            'tipo' => $validated['tipo'],
            'categoria_pai_id' => $validated['categoria_pai_id'] ?? null,
            'ativo' => (bool) $validated['ativo'],
        ];

        $imageName = $this->storeImage($imagem);
        if ($imageName) {
            $data['imagem'] = $imageName;
        }

        $categoria->update($data);

        return $categoria->refresh();
    }

    public function delete(int $categoriaId): void
    {
        $categoria = Categoria::query()
            ->withCount(['produtos', 'filhas'])
            ->findOrFail($categoriaId);

        if ($categoria->produtos_count > 0 || $categoria->filhas_count > 0) {
            throw new RuntimeException('Nao e possivel remover: ha produtos ou subcategorias vinculadas.');
        }

        $categoria->delete();
    }

    private function storeImage(?UploadedFile $imagem): ?string
    {
        if (!$imagem || !$imagem->isValid()) {
            return null;
        }

        $extension = $imagem->extension();
        $imageName = md5($imagem->getClientOriginalName() . strtotime('now')) . '.' . $extension;
        $imagem->move(public_path('img/categorias'), $imageName);

        return $imageName;
    }
}
