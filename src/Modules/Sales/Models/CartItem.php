<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'id_estoque_fk',
        'cod_produto',
        'nome_produto',
        'preco_unit',
        'quantidade',
        'subtotal_valor',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    use HasFactory;
}
