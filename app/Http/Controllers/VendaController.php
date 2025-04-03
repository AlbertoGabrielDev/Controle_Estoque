<?php

namespace App\Http\Controllers;

use App\Events\VendaRegistrada;
use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class VendaController extends Controller
{

    public function vendas()
    {
        $vendas = Venda::with('usuario')->paginate(10);
        return view('vendas.venda', compact('vendas'));
    }

    public function buscarProduto(Request $request)
    {
        $request->validate(['codigo_qr' => 'required|string']);
        
        try {
            $produto = Produto::where('qrcode', $request->codigo_qr)->firstOrFail();
            $estoque = Estoque::where('id_produto_fk', $produto->id_produto)->first();
            return response()->json([
                'success' => true,
                'produto' => [
                    'id_produto' => $produto->id_produto,
                    'nome_produto' => $produto->nome_produto,
                    'cod_produto' => $produto->cod_produto,
                    'unidade_medida' => $produto->unidade_medida,
                    'qrcode' => $produto->qrcode,
                    'preco_venda' => $estoque->preco_venda,
                    'estoque_atual' => $estoque->quantidade
                ]
            ]);
            
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado!'
            ], 404);
        }
    }

    public function verificarEstoque(Request $request)
    {
      
        $request->validate([
            'itens' => 'required|array',
            'itens.*.id_produto' => 'required|integer',
            'itens.*.quantidade' => 'required|integer|min:1'
        ]);

        try {
            $produtosSemEstoque = [];
            $estoqueValido = true;

            // Verifica cada item do carrinho
            foreach ($request->itens as $item) {
                $estoque = Estoque::where('id_produto_fk', $item['id_produto'])
                    ->first();

                if (!$estoque || $estoque->quantidade < $item['quantidade']) {
                    $produtosSemEstoque[] = [
                        'id_produto' => $item['id_produto'],
                        'estoque_atual' => $estoque ? $estoque->quantidade : 0,
                        'quantidade_solicitada' => $item['quantidade']
                    ];
                    $estoqueValido = false;
                }
            }

            if (!$estoqueValido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alguns produtos não possuem estoque suficiente',
                    'produtos_sem_estoque' => $produtosSemEstoque
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estoque disponível para todos os itens'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar estoque: ' . $e->getMessage()
            ], 500);
        }
    }
    public function registrarVenda(Request $request)
    {
        $request->validate([
            'itens' => 'required|array',
            'itens.*.id_produto' => 'required|integer',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.preco' => 'required|numeric|min:0',
            'itens.*.cod_produto' => 'required|string',
            'itens.*.unidade_medida' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $vendasRegistradas = [];
            $erros = [];

            foreach ($request->itens as $item) {
                // Verifica estoque novamente (em caso de concorrência)
                $estoque = Estoque::where('id_produto_fk', $item['id_produto'])
                    ->lockForUpdate()
                    ->first();

                if (!$estoque || $estoque->quantidade < $item['quantidade']) {
                    $erros[] = [
                        'produto' => $item['id_produto'],
                        'mensagem' => 'Estoque insuficiente',
                        'estoque_disponivel' => $estoque ? $estoque->quantidade : 0
                    ];
                    continue;
                }

                // Atualiza estoque
                $estoque->decrement('quantidade', $item['quantidade']);

                // Registra venda
                $venda = new Venda();
                $venda->id_produto_fk = $item['id_produto'];
                $venda->id_usuario_fk = auth()->id();
                $venda->quantidade = $item['quantidade'];
                $venda->preco_venda = $item['preco'];
                $venda->cod_produto = $item['cod_produto'];
                $venda->unidade_medida = $item['unidade_medida'];
                $venda->nome_produto = $item['nome'];
                $venda->save();

                $vendasRegistradas[] = $venda;
            }

            if (!empty($erros)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Alguns itens não puderam ser processados',
                    'erros' => $erros,
                    'vendas_registradas' => []
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'vendas_registradas' => $vendasRegistradas
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a venda: ' . $e->getMessage()
            ], 500);
        }
    }
   
    
    public function historicoVendas()
    {
        return view('vendas.historico_vendas');
    }
}
