<?php

namespace App\Traits;

trait HasStatus
{
    public function scopeWithStatus($query)
    {
        if (!auth()->user()->canToggleStatus()) {
            return $query->where('status', 1); 
        }
        return $query;
    }

    public function toggleStatus()
    {
        $this->status = !$this->status;
        $this->save();
        return $this;
    }
}