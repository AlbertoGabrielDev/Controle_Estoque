<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialDiscountPolicy;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CommercialDiscountPolicyRepositoryEloquent extends BaseRepository implements CommercialDiscountPolicyRepository
{
    public function model()
    {
        return CommercialDiscountPolicy::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function activeOptions(): Collection
    {
        return CommercialDiscountPolicy::query()
            ->select('id', 'nome', 'tipo', 'percentual_maximo')
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function maxPercentByType(string $tipo): float
    {
        $policy = CommercialDiscountPolicy::query()
            ->where('tipo', $tipo)
            ->where('ativo', true)
            ->orderByDesc('percentual_maximo')
            ->first();

        return $policy ? (float) $policy->percentual_maximo : 0.0;
    }
}
