<?php

namespace App\Models;


use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Imposto;
use App\Models\TabelaPreco;

class Cliente extends Model
{

    use HasStatus;
    use HasDatatableConfig;

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'codigo',
        'tipo_pessoa',
        'documento',
        'inscricao_estadual',
        'razao_social',
        'nome_fantasia',
        'nif_cif',
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
        'endereco_faturacao',
        'endereco_entrega',
        'segment_id',
        'limite_credito',
        'bloqueado',
        'tabela_preco',
        'condicao_pagamento',
        'tabela_preco_id',
        'imposto_padrao_id',
        'id_users_fk',
        'status',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'bloqueado' => 'boolean',
        'status' => 'integer',
        'ativo' => 'boolean',
        'limite_credito' => 'decimal:2',
    ];

    public function segmento(): BelongsTo
    {
        return $this->belongsTo(CustomerSegment::class, 'segment_id');
    }

    public function tabelaPreco(): BelongsTo
    {
        return $this->belongsTo(TabelaPreco::class, 'tabela_preco_id');
    }

    public function impostoPadrao(): BelongsTo
    {
        return $this->belongsTo(Imposto::class, 'imposto_padrao_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Cliente $model) {
            if (empty($model->id_users_fk) && auth()->check()) {
                $model->id_users_fk = auth()->id();
            }
        });

        static::saving(function (Cliente $model) {
            if (!is_null($model->ativo)) {
                $model->status = $model->ativo ? 1 : 0;
                return;
            }
            if (!is_null($model->status)) {
                $model->ativo = (int) $model->status === 1;
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
        $column = property_exists($this, 'statusColumn') ? $this->statusColumn : 'status';
        return $q->where($column, 1)->where('bloqueado', false);
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
    /**
     * Mapa de colunas para o DataTables (server-side).
     *
     * Cada entrada do array externo representa um **alias** que será enviado no JSON
     * para o front (e usado em DataTables columns[n].data). Para cada alias você pode
     * configurar as chaves abaixo:
     *
     * - db (string)                -> Coluna real da base (ex.: "clientes.nome").
     *                                 É a referência usada para ORDER BY e para a busca
     *                                 global (search). Mantenha simples para aproveitar
     *                                 índices do banco.
     *
     * - select (string, opcional)  -> Expressão usada no SELECT AS <alias> (ex.:
     *                                 "COALESCE(customer_segments.nome,'')", "CONCAT(codigo,' - ',nome)").
     *                                 Serve apenas para o valor mostrado no payload; ORDER/SEARCH continuam
     *                                 usando 'db' para desempenho.
     *
     * - label (string, opcional)   -> Rótulo amigável da coluna. Metadado útil para UI,
     *                                 não é utilizado pelo service.
     *
     * - order (bool)               -> Se true, permite ordenação nessa coluna. O service aplicará
     *                                 "ORDER BY <db> ASC|DESC".
     *
     * - search (bool)              -> Se true, essa coluna entra na busca global. O service aplicará
     *                                 "LOWER(<db>) LIKE %termo%".
     *
     * - join (array, opcional)     -> Define JOIN necessário para que 'db'/'select' funcionem:
     *                                 ['tabela', 'tabela.col_esq', '=', 'tabela_base.col_dir', 'left|inner|right?']
     *                                 O tipo é opcional e por padrão é 'left'. O trait evita JOIN duplicado.
     *
     * - computed (bool)            -> Se true, a coluna **não** entra no SELECT. Ela deve ser montada
     *                                 no decorate (ex.: $dt->addColumn('acoes', fn($row)=>'...')). Colunas
     *                                 computadas não são ordenáveis/pesquisáveis pelo service.
     *
     * Observações:
     * - Use 'select' para formatar a saída (COALESCE/CONCAT/FORMAT) sem perder a performance de
     *   ordenação e busca baseada em 'db'.
     * - O alias definido aqui **precisa** existir no front (DataTables columns[n].data).
     * - Para colunas HTML, lembre-se de marcá-las como raw no controller: ->make(..., rawColumns:['acoes']).
     *
     * @return array<string, array{
     *     db?: string,
     *     select?: string,
     *     label?: string,
     *     order?: bool,
     *     search?: bool,
     *     join?: array{0:string,1:string,2:string,3:string,4?:'left'|'inner'|'right'},
     *     computed?: bool
     * }>
     */
    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id_cliente", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.codigo", 'label' => 'Código', 'order' => true, 'search' => true],
            'c2' => ['db' => "COALESCE({$t}.nome_fantasia, {$t}.razao_social, {$t}.nome)", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c3' => ['db' => "COALESCE({$t}.nif_cif, {$t}.documento)", 'label' => 'NIF/CIF', 'order' => true, 'search' => true],
            'c4' => ['db' => "{$t}.whatsapp", 'label' => 'WhatsApp', 'order' => false, 'search' => true],
            'c5' => ['db' => "{$t}.uf", 'label' => 'UF', 'order' => true, 'search' => true],
            'seg' => [
                'db' => "customer_segments.nome",
                'label' => 'Segmento',
                'order' => false,
                'search' => true,
                'join' => ['customer_segments', 'customer_segments.id', "=", "{$t}.segment_id", 'left'],
            ],
            'st' => ['db' => "{$t}.ativo", 'label' => 'Ativo', 'order' => true, 'search' => false],
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
                    "{$t}.codigo",
                    "{$t}.nome",
                    "{$t}.nome_fantasia",
                    "{$t}.razao_social",
                    "{$t}.documento",
                    "{$t}.nif_cif",
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
            'ativo' => [
                'type' => 'select',
                'column' => "{$t}.ativo",
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
