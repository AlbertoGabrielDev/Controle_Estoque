<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class Estoque extends Model
{
    use HasStatus;

    protected $table = 'estoques';
    protected $primaryKey = 'id_estoque';

    protected $fillable = [
        'id_estoque',
        'quantidade',
        'localizacao',
        'data_entrega',
        'data_cadastro',
        'preco_custo',
        'preco_venda',
        'lote',
        'validade',
        'data_chegada',
        'id_produto_fk',
        'id_fornecedor_fk',
        'lote',
        'id_marca_fk',
        'localizacao',
        'created_at',
        'quantidade_aviso',
        'id_users_fk',
        'status',
        'imposto_total',
        'impostos_json',

    ];
    protected $dateFormat = 'Y-m-d';


    public static function buscarComFiltros(Request $request)
    {
        return static::with(['produtos', 'fornecedores', 'marcas'])
            ->join('produtos as p', 'estoques.id_produto_fk', '=', 'p.id_produto')
            ->join('fornecedores as f', 'estoques.id_fornecedor_fk', '=', 'f.id_fornecedor')
            ->join('marcas as m', 'estoques.id_marca_fk', '=', 'm.id_marca')
            ->join('categoria_produtos as cp', 'p.id_produto', '=', 'cp.id_produto_fk')
            ->join('categorias as c', 'cp.id_categoria_fk', '=', 'c.id_categoria')
            ->select('estoques.*')
            ->when($request->lote, fn($q, $v) => $q->where('estoques.lote', $v))
            ->when($request->quantidade, fn($q, $v) => $q->where('estoques.quantidade', $v))
            ->when($request->preco_custo, fn($q, $v) => $q->where('estoques.preco_custo', $v))
            ->when($request->preco_venda, fn($q, $v) => $q->where('estoques.preco_venda', $v))
            ->when($request->validade, fn($q, $v) => $q->whereDate('estoques.validade', $v))
            ->when($request->localizacao, fn($q, $v) => $q->where('estoques.localizacao', $v))
            ->when($request->nome_marca, fn($q, $v) => $q->where('m.nome_marca', $v))
            ->when($request->nome_fornecedor, fn($q, $v) => $q->where('f.nome_fornecedor', $v))
            ->when($request->nome_categoria, fn($q, $v) => $q->where('c.nome_categoria', $v))
            ->when($request->nome_produto, fn($q, $v) => $q->where('p.nome_produto', 'like', "%$v%"))
            ->when($request->data_cadastro, fn($q, $v) => $q->whereDate('estoques.data_cadastro', $v))
            ->when($request->data_chegada, fn($q, $v) => $q->whereDate('estoques.data_chegada', $v))
            ->when(!Gate::allows('permissao'), fn($q) => $q->where('estoques.status', 1))
            ->paginate(5);
    }

    public function getDataChegadaAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }

    public function getValidadeAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }
    public function historicos(): HasMany
    {
        return $this->hasMany(Historico::class, 'id_estoque_fk', 'id_estoque');
    }

    public function produtos(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'id_produto_fk', 'id_produto');
    }

    public function fornecedores(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor_fk', 'id_fornecedor');
    }

    public function marcas(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'id_marca_fk', 'id_marca');
    }
    use HasFactory;
}
