<?php

namespace App\Traits;

trait HasStatus
{
    public function scopeWithStatus($query)
    {
        if (!auth()->user()->canToggleStatus()) {
            return $query->where($this->getStatusColumn(), 1);
        }
        return $query;
    }

    public function toggleStatus()
    {
        $column = $this->getStatusColumn();
        $this->{$column} = !$this->{$column};
        $this->save();
        return $this;
    }

    protected function getStatusColumn(): string
    {
        return property_exists($this, 'statusColumn') ? $this->statusColumn : 'status';
    }

    public function statusColumnName(): string
    {
        return $this->getStatusColumn();
    }
}
