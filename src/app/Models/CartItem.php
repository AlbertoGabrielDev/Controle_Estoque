<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'cod_produto', 'nome_produto', 'preco_unit', 'quantidade', 'subtotal'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    use HasFactory;
}
