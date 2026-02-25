<?php

namespace Modules\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Products\Models\Produto;
use Modules\Stock\Models\Estoque;

class Venda extends Model
{
    use HasFactory;

    protected $table = 'vendas';

    protected $fillable = [
        'id_produto_fk',
        'id_estoque_fk',
        'id_usuario_fk',
        'cod_produto',
        'unidade_medida',
        'nome_produto',
        'quantidade',
        'preco_venda',
        'id_unidade_fk',
        'origem_venda',
        'created_at',
        'updated_at',
    ];

    // Relacionamento com Produto
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto_fk', 'id_produto');
    }

    // Relacionamento com User
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario_fk');
    }

    public function estoque()
    {
        return $this->belongsTo(Estoque::class, 'id_estoque_fk', 'id_estoque');
    }

}
