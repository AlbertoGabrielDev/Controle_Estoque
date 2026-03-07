<?php

namespace Modules\Categories\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\Categories\Models\Categoria;
use Modules\Categories\Repositories\CategoriaRepository;
use RuntimeException;

class CategoriaService
{
    public function __construct(private CategoriaRepository $repository)
    {
    }

    public function listForHome(bool $canViewInactive): Collection
    {
        return $this->repository->listForHome($canViewInactive);
    }

    public function listParentOptions(?int $exceptCategoriaId = null): Collection
    {
        return $this->repository->listParentOptions($exceptCategoriaId);
    }

    public function findOrFail(int $categoriaId): Categoria
    {
        return $this->repository->find($categoriaId);
    }

    public function create(array $validated, ?UploadedFile $imagem, ?int $userId): Categoria
    {
        return $this->repository->create([
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

        $this->repository->update($data, $categoriaId);

        return $categoria->refresh();
    }

    public function delete(int $categoriaId): void
    {
        $categoria = $this->repository->findOrFailWithCount($categoriaId, ['produtos', 'filhas']);

        if ($categoria->produtos_count > 0 || $categoria->filhas_count > 0) {
            throw new RuntimeException('Nao e possivel remover: ha produtos ou subcategorias vinculadas.');
        }

        $this->repository->delete($categoriaId);
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
