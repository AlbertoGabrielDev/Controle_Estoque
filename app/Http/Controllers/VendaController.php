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
    public function registrar(Request $request)
    {
        $request->validate(['codigo_qr' => 'required|string']);

        $produto = Produto::where('qrcode', $request->codigo_qr)->firstOrFail();

        try {
            DB::beginTransaction();

            $estoque = Estoque::where('id_produto_fk', $produto->id_produto)
                ->lockForUpdate()
                ->first();

            if (!$estoque || $estoque->quantidade <= 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Produto sem estoque!'], 400);
            }

            $estoque->decrement('quantidade', 1);

            $venda = new Venda();
            $venda->id_produto_fk = $produto->id_produto;
            $venda->id_usuario_fk = auth()->id();
            $venda->quantidade = 1;
            $venda->preco_venda = $estoque->preco_venda;
            $venda->cod_produto = $produto->cod_produto;
            $venda->unidade_medida = $produto->unidade_medida;
            $venda->nome_produto = $produto->nome_produto;
            $venda->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'produto' => [
                    'nome' => $produto->nome_produto,
                    'preco_venda' => $estoque->preco_venda,
                    'cod_produto' => $produto->cod_produto
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a venda! Tente novamente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function historicoVendas()
    {
        return view('vendas.historico_vendas');
    }
}
