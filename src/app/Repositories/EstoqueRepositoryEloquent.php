<?php

namespace App\Repositories;

use App\Criteria\StatusCriteria;
use App\Http\Requests\ValidacaoEstoque;
use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Fornecedor;
use App\Models\Historico;
use App\Models\Marca;
use App\Models\MarcaProduto;
use App\Models\Produto;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\EstoqueRepository;
use Carbon\Carbon;

use Illuminate\Http\Request;

/**
 * Class EstoqueRepositoryEloquent.
 *
 * @package namespace App\Repositories;
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
        $query = Estoque::select('estoques.*', 'produtos.nome_produto', 'fornecedores.nome_fornecedor')
            ->join('produtos', 'estoques.id_produto_fk', '=', 'produtos.id_produto')
            ->join('fornecedores', 'estoques.id_fornecedor_fk', '=', 'fornecedores.id_fornecedor');

        $estoques = $query->get();

        return [
            'estoques' => $estoques,
            'fornecedores' => Fornecedor::all(),
            'marcas' => Marca::all(),
            'categorias' => Categoria::all()
        ];
    }

    public function inserirEstoque(array $data)
    {
        $estoque = $this->create($data);

        MarcaProduto::create([
            'id_produto_fk' => $data['id_produto_fk'],
            'id_marca_fk' => $data['id_marca_fk'],
        ]);

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

    public function salvarEditar(ValidacaoEstoque $request, $estoqueId)
    {
        try {

            $this->update($request->validated(), $estoqueId);
            return redirect()->route('estoque.index')
                ->with('toast', [
                    'type' => 'success',
                    'message' => 'Estoque atualizado com sucesso!'
                ]);

            MarcaProduto::update(
                ['id_produto_fk' => $request->id_produto_fk],
                ['id_marca_fk' => $request->id_marca_fk]
            );
        } catch (\Exception $e) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Erro ao atualizar estoque: ' . $e->getMessage()
            ]);
        }
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
            // $backgrounds = $precos->map(function ($value, $key){
            //     return '#' . dechex(rand(0x000000 , 0xFFFFFF));
            // });
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
