<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Estoque;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendaService
{
    /* ===========================
       CONSULTAS DE PRODUTO/ESTOQUE
       =========================== */

    public function buscarProdutoPorQr(string $codigoQr): array
    {
        $produto = Produto::where('qrcode', $codigoQr)->firstOrFail();

        $preco = Estoque::where('id_produto_fk', $produto->id_produto)
            ->where('status', 1)
            ->orderByDesc('preco_venda')
            ->value('preco_venda');

        $qtdDisponivel = (int) Estoque::where('id_produto_fk', $produto->id_produto)
            ->where('status', 1)
            ->sum('quantidade');

        return [
            'id_produto'     => $produto->id_produto,
            'nome_produto'   => $produto->nome_produto,
            'cod_produto'    => $produto->cod_produto,
            'unidade_medida' => $produto->unidade_medida,
            'qrcode'         => $produto->qrcode,
            'preco_venda'    => (float) ($preco ?? 0),
            'estoque_atual'  => $qtdDisponivel,
        ];
    }

    public function verificarEstoqueItens(array $itens): array
    {
        $faltantes = [];

        foreach ($itens as $item) {
            $produtoId = (int) $item['id_produto'];
            $qtdPedida = (int) $item['quantidade'];

            $disponivel = (int) Estoque::where('id_produto_fk', $produtoId)
                ->where('status', 1)
                ->sum('quantidade');

            if ($disponivel < $qtdPedida) {
                $faltantes[] = [
                    'id_produto'             => $produtoId,
                    'estoque_atual'          => $disponivel,
                    'quantidade_solicitada'  => $qtdPedida,
                ];
            }
        }

        return $faltantes;
    }

    /* ===========================
       CARRINHO
       =========================== */

    public function obterOuCriarCarrinho(string $client): Cart
    {
        return Cart::firstOrCreate(
            ['client' => $client, 'status' => 'open'],
            ['total_valor' => 0]
        );
    }

    public function obterCarrinho(string $client): Cart
    {
        return $this->obterOuCriarCarrinho($client)->load('items');
    }

    public function adicionarItem(string $client, int $idProduto, int $quantidade): Cart
    {
        if ($quantidade < 1) {
            throw ValidationException::withMessages(['quantidade' => 'Quantidade deve ser pelo menos 1.']);
        }

        $produto = Produto::findOrFail($idProduto);

        $precoUnit = (float) (Estoque::where('id_produto_fk', $idProduto)
            ->where('status', 1)
            ->orderByDesc('preco_venda')
            ->value('preco_venda') ?? 0);

        $cart = $this->obterOuCriarCarrinho($client);

        // Se o mesmo produto já estiver no carrinho, só soma a quantidade
        $item = $cart->items()
            ->where('cod_produto', $produto->cod_produto)
            ->first();

        if ($item) {
            $item->quantidade += $quantidade;
            $item->preco_unit = $precoUnit; // mantém atualizado
            $item->subtotal_valor = $item->quantidade * $item->preco_unit;
            $item->save();
        } else {
            $cart->items()->create([
                'cod_produto'   => $produto->cod_produto,
                'nome_produto'  => $produto->nome_produto,
                'preco_unit'    => $precoUnit,
                'quantidade'    => $quantidade,
                'subtotal_valor'=> $quantidade * $precoUnit,
            ]);
        }

        $this->recalcularTotal($cart);

        return $cart->fresh('items');
    }

    public function atualizarQuantidadeItem(string $client, int $cartItemId, int $quantidade): Cart
    {
        $cart = $this->obterOuCriarCarrinho($client);

        /** @var CartItem $item */
        $item = $cart->items()->where('id', $cartItemId)->firstOrFail();

        if ($quantidade < 1) {
            // remover se a quantidade for zerada/pedida inválida
            $item->delete();
        } else {
            $item->quantidade = $quantidade;
            $item->subtotal_valor = $item->quantidade * $item->preco_unit;
            $item->save();
        }

        $this->recalcularTotal($cart);

        return $cart->fresh('items');
    }

    public function removerItem(string $client, int $cartItemId): Cart
    {
        $cart = $this->obterOuCriarCarrinho($client);
        $cart->items()->where('id', $cartItemId)->delete();

        $this->recalcularTotal($cart);

        return $cart->fresh('items');
    }

    protected function recalcularTotal(Cart $cart): void
    {
        $total = (float) $cart->items()->sum('subtotal_valor');
        $cart->total_valor = $total;
        $cart->save();
    }

    /* ===========================
       FINALIZAÇÃO (CHECKOUT)
       =========================== */

    public function finalizarVendaDoCarrinho(string $client): array
    {
        $cart = Cart::with('items')->where('client', $client)->where('status', 'open')->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['carrinho' => 'Carrinho vazio.']);
        }

        // Verificação de estoque antes de transacionar
        $faltantes = $this->verificarEstoqueItens(
            $cart->items->map(fn ($i) => [
                'id_produto' => $this->produtoIdPorCodigo($i->cod_produto),
                'quantidade' => $i->quantidade,
            ])->all()
        );

        if (!empty($faltantes)) {
            return [
                'ok' => false,
                'mensagem' => 'Alguns produtos não possuem estoque suficiente',
                'faltantes' => $faltantes,
            ];
        }

        // Transação: cria pedido, baixa estoque FIFO e fecha carrinho
        return DB::transaction(function () use ($cart, $client) {

            $order = Order::create([
                'client'      => $client,
                'cart_id'     => $cart->id,
                'status'      => 'created',
                'total_valor' => $cart->total_valor,
            ]);

            foreach ($cart->items as $ci) {
                $produtoId = $this->produtoIdPorCodigo($ci->cod_produto);

                // Baixa estoque FIFO por data_chegada (lotes antigos primeiro)
                $this->baixarEstoqueFIFO($produtoId, $ci->quantidade);

                // Cria item do pedido (atenção: coluna no banco é "sub_valor")
                $oi = new OrderItem();
                $oi->order_id    = $order->id;
                $oi->cod_produto = $ci->cod_produto;
                $oi->nome_produto= $ci->nome_produto;
                $oi->preco_unit  = $ci->preco_unit;
                $oi->quantidade  = $ci->quantidade;
                $oi->sub_valor   = $ci->subtotal_valor; // coluna real da migration
                $oi->save();
            }

            // Fecha o carrinho
            $cart->status = 'ordered';
            $cart->save();

            return [
                'ok'         => true,
                'order_id'   => $order->id,
                'total'      => (float) $order->total_valor,
                'status'     => $order->status,
            ];
        });
    }

    protected function baixarEstoqueFIFO(int $produtoId, int $qtdNecessaria): void
    {
        if ($qtdNecessaria <= 0) {
            return;
        }

        // Linhas de estoque bloqueadas para update (evita corrida)
        $lotes = Estoque::where('id_produto_fk', $produtoId)
            ->where('status', 1)
            ->orderBy('data_chegada') // FIFO
            ->lockForUpdate()
            ->get();

        $restante = $qtdNecessaria;

        foreach ($lotes as $lote) {
            if ($restante <= 0) break;

            $usa = min($lote->quantidade, $restante);

            if ($usa > 0) {
                $lote->quantidade -= $usa;
                $lote->save();
                $restante -= $usa;
            }
        }

        if ($restante > 0) {
            // Em tese não ocorre pois checamos antes, mas mantém a segurança
            throw ValidationException::withMessages([
                'estoque' => "Estoque insuficiente para o produto {$produtoId}. Faltam {$restante}."
            ]);
        }
    }

    protected function produtoIdPorCodigo(string $codProduto): int
    {
        $produto = Produto::where('cod_produto', $codProduto)->firstOrFail();
        return (int) $produto->id_produto;
    }
}
