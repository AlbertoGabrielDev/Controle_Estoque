<?php

namespace App\Repositories;

use App\Models\Produto;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ProdutoRepository.
 *
 * @package namespace App\Repositories;
 */
class ProdutoRepository 
{
   private $model; 

    public function __construct(Produto $model)
    {
        $this->model = $model;
    }

   Public function getById($id){
    return Produto::findOrFail($id);
   }

   public function getAll(){
    return Produto::all();
   }

   public function create($data)
{
    return Produto::create($data);
}

public function update($id, $data)
{
    $produto = Produto::findOrFail($id);
    $produto->update($data);
    return $produto;
}

public function delete($id)
{
    $produto = Produto::findOrFail($id);
    $produto->delete();
}
}
