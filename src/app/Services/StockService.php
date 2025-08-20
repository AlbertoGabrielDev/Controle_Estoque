<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Retorna info do produto + preço (estoque) e quantidade disponível.
     */
    public function getProductBySku(string $sku): ?array
    {
        $produto = DB::table('produtos as p')
            ->select('p.id_produto','p.cod_produto','p.nome_produto','p.status')
            ->where('p.cod_produto', $sku)
            ->first();

        if (!$produto || (int)$produto->status !== 1) {
            return null;
        }

        // Agrega estoque ativo
        $estoque = DB::table('estoque as e')
            ->selectRaw('SUM(e.quantidade) as qtd_disponivel, MAX(e.preco_venda) as preco_venda')
            ->where('e.id_produto_fk', $produto->id_produto)
            ->where('e.status', 1)
            ->first();

        return [
            'id_produto'   => $produto->id_produto,
            'cod_produto'  => $produto->cod_produto,
            'nome_produto' => $produto->nome_produto,
            'preco_venda'  => (float)($estoque->preco_venda ?? 0),
            'qtd_disponivel' => (int)($estoque->qtd_disponivel ?? 0),
        ];
    }

    /**
     * Verifica disponibilidade para uma lista de itens [{sku, qty}]
     * Retorna array com linhas resolvidas (nome, preço snapshot, subtotal) e total.
     */
    public function checkAndPrice(array $items): array
    {
        $linhas = [];
        $total = 0.0;

        foreach ($items as $i) {
            $sku = $i['sku'];
            $qty = (int)$i['qty'];
            $p   = $this->getProductBySku($sku);

            if (!$p) {
                throw new \RuntimeException("SKU {$sku} não encontrado/ativo.");
            }
            if ($p['qtd_disponivel'] < $qty) {
                throw new \RuntimeException("SKU {$sku} sem estoque suficiente. Disp: {$p['qtd_disponivel']}.");
            }
            $preco = (float)$p['preco_venda'];
            $subtotal = $preco * $qty;

            $linhas[] = [
                'cod_produto'  => $p['cod_produto'],
                'nome_produto' => $p['nome_produto'],
                'preco_unit'   => $preco,
                'quantidade'   => $qty,
                'subtotal'     => $subtotal,
            ];
            $total += $subtotal;
        }

        return ['linhas' => $linhas, 'total' => $total];
    }

    /**
     * Dá baixa simples no estoque por produto (distribui por lotes, FIFO).
     */
    public function decrementStock(string $sku, int $qty): void
    {
        $produto = DB::table('produtos')->where('cod_produto', $sku)->first();
        if (!$produto) throw new \RuntimeException("Produto {$sku} não encontrado.");

        $restante = $qty;

        $lotes = DB::table('estoque')
            ->where('id_produto_fk', $produto->id_produto)
            ->where('status', 1)
            ->where('quantidade', '>', 0)
            ->orderBy('validade') // FIFO por validade
            ->orderBy('data_chegada')
            ->lockForUpdate()
            ->get();

        foreach ($lotes as $lote) {
            if ($restante <= 0) break;

            $consumir = min($restante, (int)$lote->quantidade);
            DB::table('estoque')
                ->where('id_estoque', $lote->id_estoque)
                ->update(['quantidade' => (int)$lote->quantidade - $consumir]);
            $restante -= $consumir;
        }

        if ($restante > 0) {
            throw new \RuntimeException("Estoque insuficiente para {$sku} no momento da baixa.");
        }
    }
}
