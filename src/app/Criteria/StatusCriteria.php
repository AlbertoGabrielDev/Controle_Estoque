<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class StatusCriteria.
 *
 * @package namespace App\Criteria;
 */
class StatusCriteria implements CriteriaInterface
{
    public function apply($model, RepositoryInterface $repository)
    {
        if (method_exists($model->getModel(), 'scopeWithStatus')) {
            return $model->withStatus();
        }
        return $model;
    }
}

