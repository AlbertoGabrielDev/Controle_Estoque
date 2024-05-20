<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historico extends Model
{
    protected $table = 'historico';
    protected $primaryKey = 'historico_id';

    protected $fillable = [
        'quantidade_diminuida',
        'id_estoque_fk',
        'quantidade_historico',
        'venda'
    ];

    public function estoques(): BelongsTo
    {
        return $this->BelongsTo(Estoque::class, 'id_estoque_fk' , 'id_estoque');
    }

    use HasFactory;
}
