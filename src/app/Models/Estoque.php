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
        'created_at',
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
        $query = static::query()
            ->with(['produtos', 'fornecedores', 'marcas'])
            ->select('estoques.*');

        $query
            ->when($request->filled('lote'), fn($q) => $q->where('estoques.lote', $request->lote))
            ->when($request->filled('quantidade'), fn($q) => $q->where('estoques.quantidade', $request->quantidade))
            ->when($request->filled('preco_custo'), fn($q) => $q->where('estoques.preco_custo', $request->preco_custo))
            ->when($request->filled('preco_venda'), fn($q) => $q->where('estoques.preco_venda', $request->preco_venda))
            ->when($request->filled('validade'), fn($q) => $q->whereDate('estoques.validade', $request->validade))
            ->when($request->filled('localizacao'), fn($q) => $q->where('estoques.localizacao', $request->localizacao))
            ->when($request->filled('created_at'), fn($q) => $q->whereDate('estoques.created_at', $request->created_at))
            ->when($request->filled('data_chegada'), fn($q) => $q->whereDate('estoques.data_chegada', $request->data_chegada))
            ->when(!\Gate::allows('permissao'), fn($q) => $q->where('estoques.status', 1));
        $query
            ->when($request->filled('cod_produto'), function ($q) use ($request) {
                $q->whereIn('id_produto_fk', function ($sub) use ($request) {
                    $sub->select('id_produto')
                        ->from('produtos')
                        ->where('cod_produto', 'like', '%' . $request->cod_produto . '%');
                });
            })
            ->when($request->filled('nome_marca'), function ($q) use ($request) {
                $q->whereIn('id_marca_fk', function ($sub) use ($request) {
                    $sub->select('id_marca')
                        ->from('marcas')
                        ->where('nome_marca', $request->nome_marca);
                });
            })
            ->when($request->filled('nome_fornecedor'), function ($q) use ($request) {
                $q->whereIn('id_fornecedor_fk', function ($sub) use ($request) {
                    $sub->select('id_fornecedor')
                        ->from('fornecedores')
                        ->where('nome_fornecedor', $request->nome_fornecedor);
                });
            })
            ->when($request->filled('nome_produto'), function ($q) use ($request) {
                $q->whereIn('id_produto_fk', function ($sub) use ($request) {
                    $sub->select('id_produto')
                        ->from('produtos')
                        ->where('nome_produto', 'like', '%' . $request->nome_produto . '%');
                });
            })
            ->when($request->filled('nome_categoria'), function ($q) use ($request) {
                $q->whereIn('id_produto_fk', function ($sub) use ($request) {
                    $sub->select('cp.id_produto_fk')
                        ->from('categoria_produtos as cp')
                        ->join('categorias as c', 'cp.id_categoria_fk', '=', 'c.id_categoria')
                        ->where('c.nome_categoria', $request->nome_categoria);
                });
            });
        return $query
            ->orderByDesc('estoques.id_estoque')
            ->simplePaginate(10);
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
