<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\MessageTemplateRepository;
use App\Entities\MessageTemplate;
use App\Validators\MessageTemplateValidator;

/**
 * Class MessageTemplateRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MessageTemplateRepositoryEloquent extends BaseRepository implements MessageTemplateRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MessageTemplate::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
