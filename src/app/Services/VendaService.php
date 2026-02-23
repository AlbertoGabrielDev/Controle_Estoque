<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Cliente;
use App\Models\Estoque;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produto;
use App\Models\TabelaPreco;
use App\Models\Venda;                 // <--- inclui Venda
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendaService
{
    /* ===========================
       CONSULTAS DE PRODUTO/ESTOQUE
       =========================== */

    public function buscarProduto(?string $codigoQr = null, ?string $codigoProd = null): array
    {
        // 1) Encontrar o produto ATIVO que corresponda ao QR ou código
        //    e que TENHA pelo menos um lote ativo (status=1) com quantidade > 0.
        $produto = Produto::query()
            ->with('unidadeMedida:id,codigo')
            ->where('status', 1) // produto ativo
            ->where(function ($q) use ($codigoQr, $codigoProd) {
                if ($codigoQr) {
                    $q->orWhere('qrcode', trim($codigoQr));
                }
                if ($codigoProd) {
                    $q->orWhere('cod_produto', trim($codigoProd));
                }
            })
            ->whereExists(function ($q) {
                $q->selectRaw(1)
                    ->from('estoques')
                    ->whereColumn('estoques.id_produto_fk', 'produtos.id_produto')
                    ->where('estoques.status', 1)         // lote ativo
                    ->where('estoques.quantidade', '>', 0); // com quantidade
            })
            ->firstOrFail(); // se não achar, dispara 404 e o controller devolve "Produto não encontrado!"

        // 2) Preço = MAIOR preço de um lote ativo com quantidade > 0 (opcional: pode usar o último, etc.)
        $preco = Estoque::where('id_produto_fk', $produto->id_produto)
            ->where('status', 1)
            ->where('quantidade', '>', 0)
            ->orderByDesc('preco_venda')
            ->value('preco_venda');

        // 3) Quantidade disponível SOMENTE de lotes ativos (>0)
        $qtdDisponivel = (int) Estoque::where('id_produto_fk', $produto->id_produto)
            ->where('status', 1)
            ->where('quantidade', '>', 0)
            ->sum('quantidade');

        // (Segurança extra) Se por algum motivo a soma for 0, consideramos "não encontrado"
        if ($qtdDisponivel <= 0) {
            abort(404, 'Produto não encontrado'); // mantém o comportamento esperado no front
        }

        $unidadeCodigo = $produto->unidadeMedida?->codigo ?? $produto->unidade_medida;

        return [
            'id_produto' => $produto->id_produto,
            'nome_produto' => $produto->nome_produto,
            'cod_produto' => $produto->cod_produto,
            'unidade_medida' => $unidadeCodigo,
            'qrcode' => $produto->qrcode,
            'preco_venda' => (float) ($preco ?? 0),
            'estoque_atual' => $qtdDisponivel,
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
                    'id_produto' => $produtoId,
                    'estoque_atual' => $disponivel,
                    'quantidade_solicitada' => $qtdPedida,
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

        $tabelaPreco = $this->resolveTabelaPrecoPorCliente($client);

        $cart = $this->obterOuCriarCarrinho($client);

        $item = $cart->items()->where('cod_produto', $produto->cod_produto)->first();

        if ($item) {
            $item->quantidade += $quantidade;
            $item->preco_unit = $this->resolvePrecoUnitario($produto, $item->quantidade, $tabelaPreco);
            $item->subtotal_valor = $item->quantidade * $item->preco_unit;
            $item->save();
        } else {
            $precoUnit = $this->resolvePrecoUnitario($produto, $quantidade, $tabelaPreco);
            $cart->items()->create([
                'cod_produto' => $produto->cod_produto,
                'nome_produto' => $produto->nome_produto,
                'preco_unit' => $precoUnit,
                'quantidade' => $quantidade,
                'subtotal_valor' => $quantidade * $precoUnit,
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
            $item->delete();
        } else {
            $produto = Produto::query()
                ->where('cod_produto', $item->cod_produto)
                ->first();
            $tabelaPreco = $this->resolveTabelaPrecoPorCliente($client);
            $item->quantidade = $quantidade;
            if ($produto) {
                $item->preco_unit = $this->resolvePrecoUnitario($produto, $quantidade, $tabelaPreco);
            }
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
    public function finalizarVendaDoCarrinho(string $client): array
    {
        $cart = Cart::with('items')->where('client', $client)->where('status', 'open')->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['carrinho' => 'Carrinho vazio.']);
        }

        $faltantes = $this->verificarEstoqueItens(
            $cart->items->map(fn($i) => [
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

        return DB::transaction(function () use ($cart, $client) {

            $order = Order::create([
                'client' => $client,
                'cart_id' => $cart->id,
                'status' => 'created',
                'total_valor' => $cart->total_valor,
            ]);

            foreach ($cart->items as $ci) {
                $produtoId = $this->produtoIdPorCodigo($ci->cod_produto);

                // Baixa estoque FIFO
                $this->baixarEstoqueFIFO($produtoId, $ci->quantidade);

                // Grava item do pedido (coluna é sub_valor)
                $oi = new OrderItem();
                $oi->order_id = $order->id;
                $oi->cod_produto = $ci->cod_produto;
                $oi->nome_produto = $ci->nome_produto;
                $oi->preco_unit = $ci->preco_unit;
                $oi->quantidade = $ci->quantidade;
                $oi->sub_valor = $ci->subtotal_valor;
                $oi->save();

                // ===== Também grava na tabela VENDAS (uma linha por item) =====
                $produtoVenda = Produto::query()
                    ->with('unidadeMedida:id,codigo')
                    ->where('cod_produto', $ci->cod_produto)
                    ->first();
                $unidade = $produtoVenda?->unidadeMedida?->codigo
                    ?? $produtoVenda?->unidade_medida
                    ?? null;

                $venda = new Venda();
                $venda->id_produto_fk = $produtoId;
                $venda->id_usuario_fk = auth()->id(); // se null e a coluna não aceitar, ajuste o default no schema
                $venda->quantidade = $ci->quantidade;
                $venda->preco_venda = $ci->preco_unit;
                $venda->cod_produto = $ci->cod_produto;
                $venda->unidade_medida = $unidade ?? 'UN';
                $venda->nome_produto = $ci->nome_produto;
                $venda->id_unidade_fk = current_unidade()->id_unidade;
                $venda->save();
            }

            // Fecha carrinho
            $cart->status = 'ordered';
            $cart->save();

            // Cria (ou deixa pronto) um carrinho novo open na próxima carga
            // $this->obterOuCriarCarrinho($client); // opcional

            return [
                'ok' => true,
                'order_id' => $order->id,
                'total' => (float) $order->total_valor,
                'status' => $order->status,
            ];
        });
    }

    protected function baixarEstoqueFIFO(int $produtoId, int $qtdNecessaria): void
    {
        if ($qtdNecessaria <= 0) {
            return;
        }

        $lotes = Estoque::where('id_produto_fk', $produtoId)
            ->where('status', 1)
            ->orderBy('data_chegada')
            ->lockForUpdate()
            ->get();

        $restante = $qtdNecessaria;

        foreach ($lotes as $lote) {
            if ($restante <= 0)
                break;

            $usa = min($lote->quantidade, $restante);

            if ($usa > 0) {
                $lote->quantidade -= $usa;
                $lote->save();
                $restante -= $usa;
            }
        }

        if ($restante > 0) {
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

    private function resolveTabelaPrecoPorCliente(?string $client): ?TabelaPreco
    {
        $client = trim((string) $client);
        if ($client === '') {
            return null;
        }

        $cliente = Cliente::query()
            ->where('whatsapp', $client)
            ->first();

        if (!$cliente) {
            $digits = preg_replace('/\D+/', '', $client);
            if ($digits !== '') {
                $cliente = Cliente::query()
                    ->whereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(whatsapp,'+',''),'-',''),' ',''),'(',''),')','') = ?",
                        [$digits]
                    )
                    ->first();
            }
        }

        if (!$cliente) {
            return null;
        }

        $tabelaId = $cliente->tabela_preco_id;
        if (!$tabelaId && !empty($cliente->tabela_preco)) {
            $tabelaId = TabelaPreco::query()
                ->where('codigo', $cliente->tabela_preco)
                ->value('id');
        }

        if (!$tabelaId) {
            return null;
        }

        $hoje = Carbon::today()->toDateString();

        return TabelaPreco::query()
            ->whereKey($tabelaId)
            ->where('ativo', true)
            ->where(function ($q) use ($hoje) {
                $q->whereNull('inicio_vigencia')
                    ->orWhereDate('inicio_vigencia', '<=', $hoje);
            })
            ->where(function ($q) use ($hoje) {
                $q->whereNull('fim_vigencia')
                    ->orWhereDate('fim_vigencia', '>=', $hoje);
            })
            ->first();
    }

    private function resolvePrecoUnitario(Produto $produto, int $quantidade, ?TabelaPreco $tabelaPreco): float
    {
        $precoEstoque = (float) (Estoque::where('id_produto_fk', $produto->id_produto)
            ->where('status', 1)
            ->orderByDesc('preco_venda')
            ->value('preco_venda') ?? 0);

        if (!$tabelaPreco) {
            return $precoEstoque;
        }

        if ($tabelaPreco->tipo_alvo === 'produto') {
            $context = $this->resolveContextoProduto($produto);
            $marcaId = $context['marca_id'] ?? null;
            $fornecedorId = $context['fornecedor_id'] ?? null;

            $row = DB::table('tabela_preco_itens')
                ->where('tabela_preco_id', $tabelaPreco->id)
                ->where('produto_id', $produto->id_produto)
                ->where(function ($q) use ($marcaId) {
                    if ($marcaId) {
                        $q->whereNull('marca_id')->orWhere('marca_id', $marcaId);
                        return;
                    }
                    $q->whereNull('marca_id');
                })
                ->where(function ($q) use ($fornecedorId) {
                    if ($fornecedorId) {
                        $q->whereNull('fornecedor_id')->orWhere('fornecedor_id', $fornecedorId);
                        return;
                    }
                    $q->whereNull('fornecedor_id');
                })
                ->orderByRaw('(marca_id IS NOT NULL) + (fornecedor_id IS NOT NULL) DESC')
                ->first();
        } else {
            if (empty($produto->item_id)) {
                return $precoEstoque;
            }
            $row = DB::table('tabela_preco_itens')
                ->where('tabela_preco_id', $tabelaPreco->id)
                ->where('item_id', $produto->item_id)
                ->whereNull('marca_id')
                ->whereNull('fornecedor_id')
                ->first();
        }

        if (!$row) {
            return $precoEstoque;
        }

        $preco = (float) $row->preco;
        $quantidadeMinima = (int) ($row->quantidade_minima ?? 1);
        $desconto = (float) ($row->desconto_percent ?? 0);

        if ($quantidadeMinima > 0 && $quantidade >= $quantidadeMinima && $desconto > 0) {
            $preco = $preco * (1 - ($desconto / 100));
        }

        return round($preco, 2);
    }

    private function resolveContextoProduto(Produto $produto): array
    {
        $lote = Estoque::query()
            ->where('id_produto_fk', $produto->id_produto)
            ->where('status', 1)
            ->where('quantidade', '>', 0)
            ->orderBy('data_chegada')
            ->orderBy('id_estoque')
            ->first(['id_marca_fk', 'id_fornecedor_fk']);

        return [
            'marca_id' => $lote?->id_marca_fk,
            'fornecedor_id' => $lote?->id_fornecedor_fk,
        ];
    }
}
