<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class StockService extends VendaService
{
    /**
     * Compatibilidade para APIs de carrinho antigas (n8n).
     *
     * @param array<int, array<string, mixed>> $items
     * @return array{linhas: array<int, array<string, mixed>>, total_valor: float}
     */
    public function checkAndPrice(array $items): array
    {
        $linhas = [];
        $verificacao = [];
        $total = 0.0;

        foreach ($items as $item) {
            $sku = trim((string) ($item['sku'] ?? ''));
            $qty = (int) ($item['qty'] ?? 0);

            if ($sku === '' || $qty < 1) {
                throw ValidationException::withMessages([
                    'items' => 'Cada item deve informar sku e qty valido.',
                ]);
            }

            $resultado = $this->buscarProduto(null, $sku, null);
            $produto = $resultado['produto'] ?? null;
            $opcoes = (array) ($resultado['opcoes'] ?? []);

            if ($produto === null && $opcoes === []) {
                throw ValidationException::withMessages([
                    'items' => "Produto {$sku} nao encontrado.",
                ]);
            }

            if ($produto !== null) {
                $produtoId = (int) ($produto['id_produto'] ?? 0);
                $precoUnit = (float) ($produto['preco_venda'] ?? 0);
                $nomeProduto = (string) ($produto['nome_produto'] ?? $sku);
                $codProduto = (string) ($produto['cod_produto'] ?? $sku);
            } else {
                $first = $opcoes[0];
                $produtoId = (int) ($first['id_produto'] ?? 0);
                $precoUnit = (float) ($first['preco_venda'] ?? 0);
                $nomeProduto = (string) ($first['nome_produto'] ?? $sku);
                $codProduto = (string) ($first['cod_produto'] ?? $sku);
            }

            if ($produtoId <= 0) {
                throw ValidationException::withMessages([
                    'items' => "Produto {$sku} sem identificacao valida.",
                ]);
            }

            $verificacao[] = [
                'id_produto' => $produtoId,
                'quantidade' => $qty,
            ];

            $subtotal = round($precoUnit * $qty, 2);
            $total += $subtotal;

            $linhas[] = [
                'cod_produto' => $codProduto,
                'nome_produto' => $nomeProduto,
                'preco_unit' => $precoUnit,
                'quantidade' => $qty,
                'subtotal_valor' => $subtotal,
            ];
        }

        $faltantes = $this->verificarEstoqueItens($verificacao);
        if ($faltantes !== []) {
            throw ValidationException::withMessages([
                'items' => 'Estoque insuficiente para um ou mais itens.',
            ]);
        }

        return [
            'linhas' => $linhas,
            'total_valor' => round($total, 2),
        ];
    }

    public function decrementStock(string $codProduto, int $quantidade): void
    {
        if ($quantidade <= 0) {
            return;
        }

        $produtoId = $this->produtoIdPorCodigo($codProduto);
        $this->baixarEstoque($produtoId, $quantidade);
    }
}
