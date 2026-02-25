<?php

namespace App\Services;

use App\Enums\Canal;
use App\Enums\TipoOperacao;
use App\Models\Estoque;
use App\Repositories\EstoqueRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Products\Models\Produto;

class EstoqueService
{
    public function __construct(
        private EstoqueRepository $estoqueRepository,
        private TaxCalculatorService $taxCalculator
    ) {
    }

    public function cadastroPayload(): array
    {
        return $this->estoqueRepository->cadastro();
    }

    public function inserir(array $validated): mixed
    {
        if (empty($validated['qrcode'])) {
            $validated['qrcode'] = (string) Str::uuid();
        }

        $produtoId = (int) $validated['id_produto_fk'];
        $produto = Produto::findOrFail($produtoId);
        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $produtoId)
            ->value('id_categoria_fk');

        $ctx = $this->buildTaxContext(
            produtoId: $produtoId,
            categoriaId: $categoriaId,
            ncm: $produto->ncm ?? null,
            precoVenda: (float) ($validated['preco_venda'] ?? 0)
        );

        $raw = $this->taxCalculator->calcular($ctx);
        $ruleId = $this->extractRuleId($raw);

        $data = array_merge($validated, [
            'id_users_fk' => Auth::id(),
            'imposto_total' => $raw['_total_impostos'] ?? 0.0,
            'impostos_json' => json_encode($raw['_compact'] ?? $raw, JSON_UNESCAPED_UNICODE),
            'id_tax_fk' => $ruleId,
        ]);

        return DB::transaction(fn () => $this->estoqueRepository->inserirEstoque($data));
    }

    public function editarPayload(int $estoqueId): array
    {
        $editar = $this->estoqueRepository->editar($estoqueId);
        $estoque = $editar['estoque'];
        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $estoque->id_produto_fk)
            ->value('id_categoria_fk');

        $ctx = $this->buildTaxContext(
            produtoId: (int) $estoque->id_produto_fk,
            categoriaId: $categoriaId,
            ncm: optional($estoque->produtos)->ncm,
            precoVenda: (float) ($estoque->preco_venda ?? 0)
        );

        $raw = $this->taxCalculator->calcular($ctx);

        return array_merge($editar, [
            'previewVm' => $this->buildPreviewVm($ctx['valores']['valor'], $raw),
            'rawImpostos' => $raw,
        ]);
    }

    public function salvarEdicao(int $estoqueId, array $validated): mixed
    {
        $estoque = Estoque::findOrFail($estoqueId);

        if (array_key_exists('qrcode', $validated) && ($validated['qrcode'] === null || $validated['qrcode'] === '')) {
            unset($validated['qrcode']);
        }

        $produtoId = (int) ($validated['id_produto_fk'] ?? $estoque->id_produto_fk);
        $produto = Produto::findOrFail($produtoId);
        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $produtoId)
            ->value('id_categoria_fk');

        $ctx = $this->buildTaxContext(
            produtoId: $produtoId,
            categoriaId: $categoriaId,
            ncm: $produto->ncm ?? null,
            precoVenda: (float) ($validated['preco_venda'] ?? $estoque->preco_venda ?? 0)
        );

        $raw = $this->taxCalculator->calcular($ctx);
        $ruleId = $this->extractRuleId($raw);

        $data = array_merge($validated, [
            'impostos_json' => json_encode($raw['_compact'] ?? $raw, JSON_UNESCAPED_UNICODE),
            'imposto_total' => $raw['_total_impostos'] ?? 0,
            'id_tax_fk' => $ruleId,
        ]);

        return DB::transaction(fn () => $this->estoqueRepository->salvarEditar($data, $estoqueId));
    }

    public function calcularImpostosPreview(array $input): array
    {
        $produto = Produto::findOrFail((int) ($input['id_produto_fk'] ?? 0));
        $valor = (float) ($input['preco_venda'] ?? 0);
        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $produto->id_produto)
            ->value('id_categoria_fk');

        $ctx = $this->buildTaxContext(
            produtoId: (int) $produto->id_produto,
            categoriaId: $categoriaId,
            ncm: $produto->ncm ?? null,
            precoVenda: $valor,
            ufDestino: (string) ($input['uf_destino'] ?? config('empresa.uf_origem', 'GO'))
        );

        $raw = $this->taxCalculator->calcular($ctx);
        $vm = $this->buildPreviewVm($ctx['valores']['valor'], $raw);

        return [
            'vm' => $vm,
            'raw' => $raw,
            'meta' => [
                'total_com_impostos' => $vm['__totais']['total_com_impostos'],
                'id_tax_fk' => $this->extractRuleId($raw),
            ],
        ];
    }

    private function buildTaxContext(
        int $produtoId,
        int|string|null $categoriaId,
        string|null $ncm,
        float $precoVenda,
        string $ufDestino = ''
    ): array {
        $ufOrigem = strtoupper((string) config('empresa.uf_origem', 'GO'));
        $destino = strtoupper($ufDestino !== '' ? $ufDestino : $ufOrigem);

        return [
            'data' => now()->toDateString(),
            'ignorar_segmento' => true,
            'operacao' => [
                'tipo' => TipoOperacao::Venda->value,
                'canal' => Canal::Balcao->value,
                'uf_origem' => $ufOrigem,
                'uf_destino' => $destino,
            ],
            'escopos' => [1],
            'produto' => [
                'id' => $produtoId,
                'categoria_id' => $categoriaId,
                'ncm' => $ncm,
            ],
            'valores' => [
                'valor' => $precoVenda,
                'desconto' => 0,
                'frete' => 0,
            ],
        ];
    }

    private function buildPreviewVm(float $precoBase, array $raw): array
    {
        return [
            '__totais' => [
                'preco_base' => $raw['_total_sem_impostos'] ?? $precoBase,
                'total_impostos' => $raw['_total_impostos'] ?? 0,
                'total_com_impostos' => $raw['_total_com_impostos'] ?? ($precoBase + ($raw['_total_impostos'] ?? 0)),
            ],
            'impostos' => array_values(array_filter(
                $raw,
                fn($v, $k) => is_array($v) && !str_starts_with((string) $k, '_'),
                ARRAY_FILTER_USE_BOTH
            )),
        ];
    }

    private function extractRuleId(array $raw): ?int
    {
        foreach ($raw as $bloco) {
            if (!is_array($bloco) || empty($bloco['linhas']) || !is_array($bloco['linhas'])) {
                continue;
            }

            foreach ($bloco['linhas'] as $linha) {
                $ruleId = (int) data_get($linha, 'rule_id', data_get($linha, 'rule_dump.id', 0));
                if ($ruleId > 0) {
                    return $ruleId;
                }
            }
        }

        return null;
    }
}
