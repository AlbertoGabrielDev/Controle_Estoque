<?php

namespace App\Models;


use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{

    use HasStatus;
    use HasDatatableConfig;

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
    /** =================== CONFIG COLUMNS (LISTAGEM) =================== */
    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id_cliente", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.nome", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.documento", 'label' => 'Documento', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.whatsapp", 'label' => 'WhatsApp', 'order' => false, 'search' => true],
            'c4' => ['db' => "{$t}.uf", 'label' => 'UF', 'order' => true, 'search' => true],
            'seg' => [
                'db' => "customer_segments.nome",
                'label' => 'Segmento',
                'order' => false,
                'search' => true,
                'join' => ['customer_segments', 'customer_segments.id', "=", "{$t}.segment_id", 'left'],
            ],
            'st' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    /** =================== CONFIG FILTERS =================== */
    public static function dtFilters(): array
    {
        $t = (new static)->getTable();
        return [
            'q' => [
                'type' => 'text', //Fazer isso sumir 
                'columns' => [
                    "{$t}.nome",
                    "{$t}.nome_fantasia",
                    "{$t}.razao_social",
                    "{$t}.documento",
                    "{$t}.whatsapp",
                    "{$t}.email",
                ],
            ],
            'uf' => [
                'type' => 'select',
                'column' => "{$t}.uf",
                'operator' => '=',
                'transform' => 'upper',
                'nullable' => true,
            ],
            'segment_id' => [
                'type' => 'select',
                'column' => "{$t}.segment_id",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
            // Exemplo extra:
            // 'created_at' => ['type'=>'date','column'=>"{$t}.created_at"],
            // 'created_range' => ['type'=>'daterange','column'=>"{$t}.created_at",'start_param'=>'created_from','end_param'=>'created_to'],
        ];
    }

    /** (Opcional) escopo para status=1, caso use na Criteria */
    public function scopeWithStatus($q)
    {
        return $q->where($this->getTable() . '.status', 1);
    }

}
