<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TaxRuleRepository;
use App\Models\TaxRule;
use App\Validators\TaxRuleValidator;

/**
 * Class TaxRuleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
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
