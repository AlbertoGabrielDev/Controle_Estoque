<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{

    use HasStatus;          
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'tipo_pessoa',
        'documento',
        'inscricao_estadual',
        'razao_social',
        'nome_fantasia',
        'nome',
        'email',
        'whatsapp',
        'telefone',
        'site',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'pais',
        'segment_id',
        'limite_credito',
        'bloqueado',
        'tabela_preco',
        'id_users_fk',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'bloqueado' => 'boolean',
        'status' => 'integer',
        'limite_credito' => 'decimal:2',
    ];

    public function segmento(): BelongsTo
    {
        return $this->belongsTo(CustomerSegment::class, 'segment_id');
    }


    protected static function booted(): void
    {
        static::creating(function (Cliente $model) {
            if (empty($model->id_users_fk) && auth()->check()) {
                $model->id_users_fk = auth()->id();
            }
        });
    }

    // Pedidos de venda (seu 'orders')
    public function pedidos(): HasMany
    {
        return $this->hasMany(Order::class, 'cliente_id', 'id_cliente'); // Criar o id_cliente na tabela orders
    }

    public function carrinhos(): HasMany
    {
        return $this->hasMany(Cart::class, 'cliente_id', 'id_cliente'); // Criar o id_cliente na tabela carts
    }

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class, 'cliente_id', 'id_cliente'); // Criar o id_cliente na tabela vendas
    }

    // public function enderecos(): HasMany
    // {
    //     return $this->hasMany(ClienteEndereco::class, 'id_cliente_fk', 'id_cliente');
    // }
    // public function contatos(): HasMany
    // {
    //     return $this->hasMany(ClienteContato::class, 'id_cliente_fk', 'id_cliente');
    // }

    public function scopeAtivos($q)
    {
        return $q->where('status', 1)->where('bloqueado', false);
    }

    public function scopePorDocumento($q, ?string $doc)
    {
        if (!$doc)
            return $q;
        return $q->where('documento', $doc);
    }

    public function scopeBuscar($q, ?string $termo)
    {
        if (!$termo)
            return $q;
        $t = "%{$termo}%";
        return $q->where(function ($s) use ($t) {
            $s->where('nome', 'like', $t)
                ->orWhere('nome_fantasia', 'like', $t)
                ->orWhere('razao_social', 'like', $t)
                ->orWhere('whatsapp', 'like', $t)
                ->orWhere('email', 'like', $t);
        });
    }
}
