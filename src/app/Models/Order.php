<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['client', 'cart_id', 'status', 'total_valor'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    use HasFactory;
}
