<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    protected $fillable = ['order_id', 'cod_produto', 'nome_produto', 'preco_unit', 'quantidade', 'subtotal'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    use HasFactory;
}
