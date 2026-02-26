<?php

namespace Modules\Purchases\Database\Seeders;

use App\Models\Fornecedor;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseQuotationSupplierItem;
use Modules\Purchases\Models\PurchaseRequisition;
use Modules\Purchases\Services\PurchaseOrderService;
use Modules\Purchases\Services\PurchaseQuotationService;
use Modules\Purchases\Services\PurchaseReceiptService;
use Modules\Purchases\Services\PurchaseRequisitionService;
use Modules\Purchases\Services\PurchaseReturnService;

class PurchasesSeeder extends Seeder
{
    /**
     * Run purchases module seed data.
     *
     * @return void
     * @throws \Throwable
     */
    public function run(): void
    {
        if (PurchaseRequisition::query()->exists()) {
            if ($this->command) {
                $this->command->warn('PurchasesSeeder skipped: purchase_requisitions already has data.');
            }
            return;
        }

        $user = User::query()->first() ?? User::factory()->create();

        $suppliers = $this->ensureSuppliers($user, 2);
        $items = $this->ensureItems(2);

        $requisitionService = app(PurchaseRequisitionService::class);

        $requisition = $requisitionService->createRequisition([
            'observacoes' => 'Seed requisition',
            'data_requisicao' => Carbon::today()->toDateString(),
            'items' => [
                $this->makeRequisitionItemPayload($items[0], 10, 12.5),
                $this->makeRequisitionItemPayload($items[1], 5, 18.0),
            ],
        ], $user->id);

        $requisitionService->approveRequisition($requisition->id);

        $quotationService = app(PurchaseQuotationService::class);

        $quotation = $quotationService->createFromRequisition($requisition->id, [
            'data_limite' => Carbon::today()->addDays(7)->toDateString(),
            'observacoes' => 'Seed quotation',
            'supplier_ids' => array_map(
                fn(Fornecedor $supplier): int => $supplier->id_fornecedor,
                $suppliers
            ),
        ])->load(['requisition.items', 'suppliers.items']);

        foreach ($quotation->suppliers as $index => $quotationSupplier) {
            $payloadItems = [];

            foreach ($quotation->requisition->items as $reqItem) {
                $basePrice = (float) ($reqItem->preco_estimado ?? 0);
                if ($basePrice <= 0) {
                    $basePrice = 10.0;
                }

                $payloadItems[] = [
                    'requisition_item_id' => $reqItem->id,
                    'quantidade' => $reqItem->quantidade,
                    'preco_unit' => $basePrice + ($index * 1.5),
                    'imposto_id' => $reqItem->imposto_id,
                    'aliquota_snapshot' => null,
                ];
            }

            $quotationService->registerSupplierPrices($quotationSupplier->id, $payloadItems);
        }

        foreach ($quotation->requisition->items as $reqItem) {
            $winner = PurchaseQuotationSupplierItem::query()
                ->where('requisition_item_id', $reqItem->id)
                ->whereHas('quotationSupplier', function ($query) use ($quotation) {
                    $query->where('quotation_id', $quotation->id);
                })
                ->orderBy('preco_unit')
                ->first();

            if ($winner) {
                $quotationService->selectWinnerForItem($quotation->id, $winner->id);
            }
        }

        $quotationService->closeQuotation($quotation->id);

        $orderService = app(PurchaseOrderService::class);
        $orders = $orderService->createFromQuotation($quotation->id);

        $receiptService = app(PurchaseReceiptService::class);
        $receipts = [];

        foreach ($orders as $order) {
            $order->load('items');

            $receiptItems = [];
            foreach ($order->items as $orderItem) {
                $receiptItems[] = [
                    'order_item_id' => $orderItem->id,
                    'quantidade_recebida' => $orderItem->quantidade_pedida,
                    'preco_unit_recebido' => $orderItem->preco_unit,
                    'imposto_id' => $orderItem->imposto_id,
                    'aliquota_snapshot' => $orderItem->aliquota_snapshot,
                ];
            }

            $receipt = $receiptService->registerReceipt([
                'order_id' => $order->id,
                'data_recebimento' => Carbon::today()->toDateString(),
                'observacoes' => 'Seed receipt',
                'items' => $receiptItems,
            ]);

            $receiptService->checkReceipt($receipt->id);
            $receipts[] = $receipt;
        }

        $firstReceipt = $receipts[0] ?? null;
        if ($firstReceipt) {
            $firstReceipt->load('items');
            $firstItem = $firstReceipt->items->first();

            if ($firstItem) {
                $returnService = app(PurchaseReturnService::class);

                $return = $returnService->createReturn([
                    'receipt_id' => $firstReceipt->id,
                    'order_id' => $firstReceipt->order_id,
                    'motivo' => 'Seed return',
                    'data_devolucao' => Carbon::today()->toDateString(),
                    'items' => [
                        [
                            'receipt_item_id' => $firstItem->id,
                            'order_item_id' => $firstItem->order_item_id,
                            'item_id' => $firstItem->item_id,
                            'quantidade_devolvida' => min(1, (float) $firstItem->quantidade_recebida),
                            'observacoes' => null,
                        ],
                    ],
                ]);

                $returnService->confirmReturn($return->id);
            }
        }

        if ($this->command) {
            $this->command->info('PurchasesSeeder completed.');
        }
    }

    /**
     * Ensure that the required number of suppliers exists.
     *
     * @param User $user
     * @param int $count
     * @return array<int, Fornecedor>
     */
    private function ensureSuppliers(User $user, int $count): array
    {
        $existing = Fornecedor::query()->take($count)->get();
        $missing = $count - $existing->count();
        $start = $existing->count();

        for ($i = 0; $i < $missing; $i++) {
            $suffix = 'S' . (string) ($start + $i + 1);
            $existing->push($this->createSupplier($user, $suffix));
        }

        return $existing->all();
    }

    /**
     * Ensure that the required number of items exists.
     *
     * @param int $count
     * @return array<int, Item>
     */
    private function ensureItems(int $count): array
    {
        $existing = Item::query()->take($count)->get();
        $missing = $count - $existing->count();
        $start = $existing->count();

        for ($i = 0; $i < $missing; $i++) {
            $suffix = 'I' . (string) ($start + $i + 1);
            $existing->push($this->createItem($suffix));
        }

        return $existing->all();
    }

    /**
     * Build a requisition item payload for the service.
     *
     * @param Item $item
     * @param float $quantity
     * @param float $price
     * @return array<string, mixed>
     */
    private function makeRequisitionItemPayload(Item $item, float $quantity, float $price): array
    {
        return [
            'item_id' => $item->id,
            'descricao_snapshot' => $item->nome,
            'unidade_medida_id' => $item->unidade_medida_id,
            'quantidade' => $quantity,
            'preco_estimado' => $price,
            'imposto_id' => null,
            'observacoes' => null,
        ];
    }

    /**
     * Create a supplier record.
     *
     * @param User $user
     * @param string $suffix
     * @return Fornecedor
     */
    private function createSupplier(User $user, string $suffix): Fornecedor
    {
        $code = $this->generateUniqueSupplierCode();
        $cnpj = $this->generateUniqueCnpj();

        return Fornecedor::query()->create([
            'codigo' => $code,
            'razao_social' => 'Supplier ' . $suffix,
            'nome_fornecedor' => 'Supplier ' . $suffix,
            'cnpj' => $cnpj,
            'nif_cif' => 'NIF' . $suffix,
            'cep' => '01001-000',
            'logradouro' => 'Rua A',
            'bairro' => 'Centro',
            'numero_casa' => '123',
            'cidade' => 'Sao Paulo',
            'uf' => 'SP',
            'email' => 'supplier_' . strtolower($suffix) . '@example.com',
            'id_users_fk' => $user->id,
            'ativo' => true,
            'status' => 1,
        ]);
    }

    /**
     * Create an item record.
     *
     * @param string $suffix
     * @return Item
     */
    private function createItem(string $suffix): Item
    {
        $sku = $this->generateUniqueSku();

        return Item::query()->create([
            'sku' => $sku,
            'nome' => 'Item ' . $suffix,
            'tipo' => 'produto',
            'preco_base' => 12.5,
            'custo' => 6.5,
            'ativo' => true,
        ]);
    }

    /**
     * Generate a unique supplier code.
     *
     * @return string
     */
    private function generateUniqueSupplierCode(): string
    {
        do {
            $code = 'FOR' . Str::upper(Str::random(6));
        } while (Fornecedor::query()->where('codigo', $code)->exists());

        return $code;
    }

    /**
     * Generate a unique CNPJ value.
     *
     * @return string
     */
    private function generateUniqueCnpj(): string
    {
        static $sequence = 10;

        do {
            $suffix = str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
            $sequence = $sequence >= 99 ? 10 : $sequence + 1;
            $cnpj = '12.345.678/0001-' . $suffix;
        } while (Fornecedor::query()->where('cnpj', $cnpj)->exists());

        return $cnpj;
    }

    /**
     * Generate a unique SKU value.
     *
     * @return string
     */
    private function generateUniqueSku(): string
    {
        do {
            $sku = 'SKU-' . Str::upper(Str::random(8));
        } while (Item::query()->where('sku', $sku)->exists());

        return $sku;
    }
}