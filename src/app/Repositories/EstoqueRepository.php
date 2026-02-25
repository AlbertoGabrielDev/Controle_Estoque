<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface EstoqueRepository.
 *
 * @package namespace App\Repositories;
 */
interface EstoqueRepository extends RepositoryInterface
{
    public function cadastro();

    public function inserirEstoque(array $data);

    public function editar($estoqueId);

    public function salvarEditar(array $data, $estoqueId);
}
