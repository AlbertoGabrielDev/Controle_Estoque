<?php

namespace App\Repositories\Traits;

use App\Repositories\CategoriaRepository;
use App\Repositories\FornecedoresRepository;
use App\Repositories\MarcaRepository;
use App\Repositories\ProdutoRepository;

trait FilterTrait {
    protected function applyLikeConditions($query, $searchLike)
    {
 
        if ($searchLike) {
            foreach ($this->fieldSearchable as $field => $operator) {
    
 
                if ($operator === 'like') {
 
                    $query->orWhere($field, 'LIKE', '%' . $searchLike . '%');
                }
            }
        }
    }
    
 
 protected function getCombinedFieldSearchable()
 {
     $combinedFieldSearchable = $this->prepareFieldSearchable($this->fieldSearchable, $this->model());
 
     $produtosRepository = app(ProdutoRepository::class);
     $fornecedoresRepository = app(FornecedoresRepository::class);
     $marcaRepository = app(MarcaRepository::class);
     $categoriaRepository = app(CategoriaRepository::class);
 
     $combinedFieldSearchable = array_merge(
         $combinedFieldSearchable,
         $this->prepareFieldSearchable($fornecedoresRepository->fieldSearchable, $fornecedoresRepository->model()),
         $this->prepareFieldSearchable($marcaRepository->fieldSearchable, $marcaRepository->model()),
         $this->prepareFieldSearchable($categoriaRepository->fieldSearchable, $categoriaRepository->model()),
         $this->prepareFieldSearchable($produtosRepository->fieldSearchable, $produtosRepository->model())
     );
 
     return $combinedFieldSearchable;
 }
 
 protected function prepareFieldSearchable(array $fieldSearchable, string $model)
 {
     return array_map(function ($operator) use ($model) {
         return [
             'operator' => $operator,
             'model' => $model,
         ];
     }, $fieldSearchable);
 }
 
 protected function applyFilters()
 {
     $modelClass = $this->model();
     $query = $modelClass::query();
     $tableName = (new $modelClass)->getTable();
     $fieldSearchable = $this->getCombinedFieldSearchable();
     $filters = array_filter(request()->all(), function ($value) {
         return !is_null($value) && $value !== '';
     });
     $joinedTables = [];
 
     foreach ($filters as $field => $value) {
         if (!isset($fieldSearchable[$field])) {
             continue;
         }
 
         $operator = $fieldSearchable[$field]['operator'];
         $relatedModelClass = $fieldSearchable[$field]['model'];
         $relatedModelInstance = new $relatedModelClass;
         $relatedTable = $relatedModelInstance->getTable();
         $qualifiedField = "$relatedTable.$field";
 
         if ($relatedTable !== $tableName && !in_array($relatedTable, $joinedTables)) {
             $query->leftJoin(
                 $relatedTable,
                 "$relatedTable.id_{$relatedTable}",
                 '=',
                 "$tableName.id_{$relatedTable}_fk"
             );
             $joinedTables[] = $relatedTable;
         }
 
         // Ajusta o valor para LIKE
         if ($operator === 'like') {
             $value = '%' . $value . '%';
         }
 
         $query->where($qualifiedField, $operator, $value);
 
         \Log::info('Applying filter', [
             'field' => $qualifiedField,
             'operator' => $operator,
             'value' => $value,
         ]);
     }
 
     // Depuração da Query Completa
     $querySql = $query->toSql();
     $queryBindings = $query->getBindings();
 
     \Log::info('Query gerada:', [
         'sql' => $querySql,
         'bindings' => $queryBindings,
         'filters' => $filters,
     ]);
 
     return $query;
 }
 
}