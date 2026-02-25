<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    protected $fillable = ['order_id', 'cod_produto', 'nome_produto', 'preco_unit', 'quantidade', 'sub_valor'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    use HasFactory;
}
