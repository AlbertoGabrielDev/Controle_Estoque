<?php

namespace Modules\Stock\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface EstoqueRepository.
 *
 * @package namespace Modules\Stock\Repositories;
 */
interface EstoqueRepository extends RepositoryInterface
{
    public function cadastro();

    public function inserirEstoque(array $data);

    public function editar($estoqueId);

    public function salvarEditar(array $data, $estoqueId);
}
