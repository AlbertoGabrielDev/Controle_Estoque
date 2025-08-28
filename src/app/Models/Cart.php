<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['client', 'status', 'total_valor'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
    use HasFactory;
}
