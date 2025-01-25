<?php

namespace App\Repositories\Traits;

use App\Repositories\CategoriaRepository;
use App\Repositories\FornecedoresRepository;
use App\Repositories\MarcaRepository;
use App\Repositories\ProdutoRepository;

use ReflectionClass;

trait FilterTrait
{
    public $hasNestedFilters = false;
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
            $operator = $fieldSearchable[$field]['operator'];
            $relatedModelClass = $fieldSearchable[$field]['model'];
            $relatedModelInstance = new $relatedModelClass;
            $relatedTable = $relatedModelInstance->getTable();
            $qualifiedField = "$relatedTable.$field";
            if ($this->isRelatedField($field)) {
                list($column, $segments) = $this->segments($fieldSearchable);
           
            
                $lastRelatedTable = $this->buildNestedJoins($query, $segments, $tableName);
                $qualifiedField = "$lastRelatedTable.$column";
    
                $query->where($qualifiedField, $operator, $value);
                return $query;
            }
          
            // /-------------------------------------------
            $query->leftJoin(
                $relatedTable,
                "$relatedTable.id_{$relatedTable}",
                '=',
                "$tableName.id_{$relatedTable}_fk"
            );
            $joinedTables[] = $relatedTable;

            if ($operator === 'like') {
                $value = '%' . $value . '%';
            }

            $query->where($qualifiedField, $operator, $value);
        }

        return $query;
    }

    protected function isRelatedField($field)
{
  
    $relatedField = 'related_' . $field;
    return request()->has($relatedField) && request()->get($relatedField) === 'true';
}

    public function segments($fieldSearchable)
    {
        foreach ($fieldSearchable as $key => $values) {
            if (strpos($key, '.') !== false) {
                $segments = explode('.', $key);
                $column = array_pop($segments);
                return [$column, $segments];
            }
        }
    }

    protected function buildNestedJoins($query, $relations, $baseTable)
    {

        $currentTable = $baseTable;
        $currentModel = $this->model();
        foreach ($relations as $relation) {

            $relatedModelClass = $this->getModelForRelation($currentTable, $relation, $currentModel);

            $relationInstance = (new $currentModel)->$relation();

            if ($relationInstance instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {

                $pivotTable = $relationInstance->getTable();
                $foreignPivotKey = $relationInstance->getForeignPivotKeyName();
                $relatedPivotKey = $relationInstance->getRelatedPivotKeyName();
                $relatedTable = $relationInstance->getRelated()->getTable();
                $ownerKey = $relationInstance->getRelated()->getKeyName();

                // Adiciona join com a tabela pivot
                if (!in_array($pivotTable, $query->getQuery()->joins ?? [])) {
                    $query->leftJoin(
                        $pivotTable,
                        "$currentTable.id_$currentTable",
                        '=',
                        "$pivotTable.$foreignPivotKey"
                    );
                }

                // Adiciona join com a tabela relacionada
                if (!in_array($relatedTable, $query->getQuery()->joins ?? [])) {
                    $query->leftJoin(
                        $relatedTable,
                        "$pivotTable.$relatedPivotKey",
                        '=',
                        "$relatedTable.$ownerKey"
                    );
                }

                $currentTable = $relatedTable;
            } else {

                $relatedInstance = new $relatedModelClass;
                $relatedTable = $relatedInstance->getTable();
                $foreignKey = $relationInstance->getForeignKeyName();
                $ownerKey = $relationInstance->getOwnerKeyName();

                if (!in_array($relatedTable, $query->getQuery()->joins ?? [])) {
                    $query->leftJoin(
                        $relatedTable,
                        "$currentTable.$foreignKey",
                        '=',
                        "$relatedTable.$ownerKey"
                    );
                }

                $currentTable = $relatedTable;
            }

            $currentModel = $relatedModelClass;
        }

        return $currentTable;  // Retorna a última tabela (categoria, por exemplo)
    }
    protected function getModelForRelation($currentTable, $relation, $currentModel = null)
    {
        $modelInstance = $currentModel ? new $currentModel : new $this->model();

        if (!method_exists($modelInstance, $relation)) {
            throw new \Exception("Relacionamento desconhecido: $relation em $currentTable");
        }

        $relationInstance = $modelInstance->$relation();

        if (!($relationInstance instanceof \Illuminate\Database\Eloquent\Relations\Relation)) {
            throw new \Exception("O método $relation não é um relacionamento válido.");
        }

        return get_class($relationInstance->getRelated());
    }
}
