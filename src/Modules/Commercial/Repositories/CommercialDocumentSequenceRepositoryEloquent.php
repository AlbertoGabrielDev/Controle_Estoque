<?php

namespace Modules\Commercial\Repositories;

use Modules\Commercial\Models\CommercialDocumentSequence;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CommercialDocumentSequenceRepositoryEloquent extends BaseRepository implements CommercialDocumentSequenceRepository
{
    public function model()
    {
        return CommercialDocumentSequence::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function lockForUpdate(string $type): CommercialDocumentSequence
    {
        return CommercialDocumentSequence::query()
            ->where('type', $type)
            ->lockForUpdate()
            ->firstOrFail();
    }
}
