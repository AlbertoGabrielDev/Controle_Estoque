<?php

namespace Modules\Products\Services;

use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Products\Models\Produto;
use Modules\Products\Repositories\ProdutoRepository;

class ProdutoService
{
    public function __construct(
        private ProdutoRepository $repository,
        private DataTableService $dataTableService
    ) {
    }

    public function datatable(Request $request): JsonResponse
    {
        [$query, $columnsMap] = Produto::makeDatatableQuery($request);

        return $this->dataTableService->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('produtos.editar', $row->id),
                        DataTableActions::status('produto.status', 'produto', $row->id, (bool) $row->st),
                    ], 'end');
                });
            }
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function cadastroPayload(): array
    {
        return $this->repository->cadastroPayload();
    }

    public function inserir(array $validated, ?int $userId): Produto
    {
        $validated['inf_nutriente'] = $this->normalizeNutrition($validated['inf_nutriente'] ?? null);
        $validated['id_users_fk'] = $userId;

        return DB::transaction(fn () => $this->repository->createProduto($validated));
    }

    /**
     * @return array<string, mixed>
     */
    public function editarPayload(int $produtoId): array
    {
        return $this->repository->editarPayload($produtoId);
    }

    public function salvarEdicao(int $produtoId, array $validated): Produto
    {
        $validated['inf_nutriente'] = $this->normalizeNutrition($validated['inf_nutriente'] ?? null);

        return DB::transaction(fn () => $this->repository->updateProduto($produtoId, $validated));
    }

    public function search(string $q)
    {
        return $this->repository->searchAtivos($q);
    }

    private function normalizeNutrition(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [$this->makeNutritionItem('Texto', trim($value), null)];
        }

        $normalized = $this->normalizeNutritionValue($decoded);

        return $normalized !== [] ? $normalized : null;
    }

    private function normalizeNutritionValue(mixed $decoded): array
    {
        if (is_array($decoded)) {
            if (array_is_list($decoded)) {
                $items = [];
                foreach ($decoded as $item) {
                    $normalized = $this->normalizeNutritionItem($item);
                    if ($normalized !== null) {
                        $items[] = $normalized;
                    }
                }

                return $items;
            }

            $items = [];
            foreach ($decoded as $key => $value) {
                $items[] = $this->makeNutritionItem(
                    $this->labelFromKey((string) $key),
                    $value,
                    $this->unitFromKey((string) $key),
                );
            }

            return $items;
        }

        return [$this->makeNutritionItem('Texto', $decoded, null)];
    }

    private function normalizeNutritionItem(mixed $item): ?array
    {
        if ($item === null || $item === '') {
            return null;
        }

        if (!is_array($item)) {
            return $this->makeNutritionItem('Item', $item, null);
        }

        $label = $item['label'] ?? $item['nome'] ?? $item['chave'] ?? $item['key'] ?? $item['nutriente'] ?? null;
        $value = $item['valor'] ?? $item['value'] ?? $item['quantidade'] ?? $item['qtd'] ?? null;
        $unit = $item['unidade'] ?? $item['unit'] ?? null;

        if ($label === null && $value === null) {
            return null;
        }

        $labelText = is_string($label) && $label !== '' ? $label : 'Item';

        return $this->makeNutritionItem($labelText, $value, $unit);
    }

    private function makeNutritionItem(string $label, mixed $valor, ?string $unidade): array
    {
        return [
            'label' => $label,
            'valor' => $valor,
            'unidade' => $unidade,
        ];
    }

    private function labelFromKey(string $key): string
    {
        $clean = trim(str_replace('_', ' ', $key));

        return $clean === '' ? 'Item' : ucwords($clean);
    }

    private function unitFromKey(string $key): ?string
    {
        $map = [
            'calorias' => 'kcal',
            'proteina' => 'g',
            'carboidrato' => 'g',
            'gordura' => 'g',
            'fibra' => 'g',
            'sodio' => 'mg',
            'acucar' => 'g',
        ];

        $k = strtolower(trim($key));

        return $map[$k] ?? null;
    }
}
