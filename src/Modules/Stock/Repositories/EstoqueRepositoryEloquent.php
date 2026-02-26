<?php

namespace Modules\Stock\Repositories;

use App\Criteria\StatusCriteria;
use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Historico;
use App\Models\Marca;
use App\Models\MarcaProduto;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Modules\Products\Models\Produto;
use Modules\Stock\Models\Estoque;
use Prettus\Repository\Eloquent\BaseRepository;

use Illuminate\Http\Request;

/**
 * Class EstoqueRepositoryEloquent.
 *
 * @package namespace Modules\Stock\Repositories;
 */
class EstoqueRepositoryEloquent extends BaseRepository implements EstoqueRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Estoque::class;
    }

    public function index()
    {
        $estoques = Estoque::with(['produtos', 'fornecedores'])
            ->orderByDesc('id_estoque')
            ->paginate(10); 

        return [
            'estoques' => $estoques,
            'fornecedores' => Fornecedor::all(),
            'marcas' => Marca::all(),
            'categorias' => Categoria::all(),
        ];
    }

    public function inserirEstoque(array $data)
    {
        // $data = current_unidade()->id_unidade_fk ?? 1;
        if (empty($data['qrcode'])) {
            $data['qrcode'] = (string) Str::uuid();
        }
        $estoque = $this->create($data);

        if (!empty($data['id_produto_fk']) && !empty($data['id_marca_fk'])) {
            MarcaProduto::create([
                'id_produto_fk' => $data['id_produto_fk'],
                'id_marca_fk' => $data['id_marca_fk'],
            ]);
        }

        return $estoque;
    }

    public function historico()
    {
        $historicos = Historico::with('estoques')->get();

        return $historicos;
    }

    public function cadastro()
    {
        $produtos = Produto::all();
        $marcas = Marca::all();
        $fornecedores = Fornecedor::all();
        return compact('fornecedores', 'marcas', 'produtos');
    }

    public function buscar(Request $request)
    {
        return [
            'estoques' => Estoque::buscarComFiltros($request),
            'fornecedores' => Fornecedor::all(),
            'marcas' => Marca::all(),
            'categorias' => Categoria::all()
        ];
    }

    public function editar($estoqueId)
    {

        $estoque = $this->findWithRelations($estoqueId, ['produtos', 'fornecedores', 'marcas']);

        return [
            'estoque' => $estoque,
            'fornecedores' => Fornecedor::all(),
            'marcas' => Marca::all(),
            'produtos' => Produto::all()
        ];

    }

    public function salvarEditar(array $data, $estoqueId)
    {
        $estoque = $this->update($data, $estoqueId);

        if (!empty($data['id_produto_fk']) && !empty($data['id_marca_fk'])) {
            MarcaProduto::query()->updateOrCreate(
                ['id_produto_fk' => $data['id_produto_fk']],
                ['id_marca_fk' => $data['id_marca_fk']]
            );
        }

        return $estoque;
    }
    public function findWithRelations($id, array $relations)
    {
        return Estoque::with($relations)->findOrFail($id);
    }

    // public function status($statusId)
    // {
    //     $status = Estoque::findOrFail($statusId);
    //     $status->status = ($status->status == 1) ? 0 : 1;
    //     $status->save();
    //     return $status;
    // }

    public function graficoFiltro($startDate, $endDate): array
    {
        if ($startDate && $endDate) {

            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            $vendas = Historico::selectRaw('DATE(created_at) as data, SUM(venda) as venda')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('data')
                ->orderBy('data')
                ->get();

            $labels = $vendas->pluck('data')->map(function ($data) {
                return Carbon::parse($data)->format('d/m/Y');
            });

            $values = $vendas->map(function ($order) {
                return number_format($order->venda, 0, '', '');
            });
        } else {
            $vendas = Historico::selectRaw('MONTH(created_at) as mes, SUM(venda) as venda')
                ->groupBy('mes')
                ->orderBy('mes')
                ->get();

            $labels = $vendas->pluck('mes')->map(function ($mes) {
                return Carbon::create()->month($mes)->format('F'); // Nome do mês
            });

            $values = $vendas->map(function ($order, $key) {
                return number_format($order->venda, 0, '', '');
            });
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(StatusCriteria::class));
        parent::boot();
    }
}
