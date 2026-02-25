<?php

namespace Modules\Taxes\Repositories;

use Modules\Taxes\Models\TaxRule;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class TaxRuleRepositoryEloquent.
 *
 * @package namespace Modules\Taxes\Repositories;
 */
class TaxRuleRepositoryEloquent extends BaseRepository implements TaxRuleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TaxRule::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
