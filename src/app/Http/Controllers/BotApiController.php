<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Order;
use App\Models\Produto;
use App\Models\TabelaPreco;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BotApiController extends Controller
{
    public function products(Request $request): JsonResponse
    {
        $term = trim((string) ($request->query('search', $request->query('q', ''))));

        $query = $this->catalogQuery($term, false);
        $rows = $query->limit(50)->get();
        $products = $rows->map(fn ($row): array => $this->mapCatalogRow($row))->values()->all();

        return response()->json([
            'products' => $products,
            'count' => count($products),
        ]);
    }

    public function product(int $id): JsonResponse
    {
        $row = $this->catalogQuery('', false)
            ->where('p.id_produto', $id)
            ->first();

        if (! $row) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'product' => $this->mapCatalogRow($row),
        ]);
    }

    public function stock(Request $request): JsonResponse
    {
        $productId = (int) $request->query('product_id', 0);
        if ($productId <= 0) {
            return response()->json([
                'stock' => [],
                'total_quantity' => 0,
            ]);
        }

        $rows = DB::table('estoques as e')
            ->join('produtos as p', 'p.id_produto', '=', 'e.id_produto_fk')
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidade_medida_id')
            ->where('e.status', 1)
            ->where('e.id_produto_fk', $productId)
            ->orderByDesc('e.id_estoque')
            ->get([
                'e.id_estoque',
                'e.id_produto_fk',
                'p.cod_produto',
                'p.nome_produto',
                'p.unidade_medida',
                'um.codigo as unidade_codigo',
                'e.preco_venda',
                'e.quantidade',
                'e.localizacao',
                'e.lote',
            ]);

        $stock = $rows->map(function ($row): array {
            return [
                'stock_id' => (int) $row->id_estoque,
                'product_id' => (int) $row->id_produto_fk,
                'product_code' => (string) $row->cod_produto,
                'product_name' => (string) $row->nome_produto,
                'unit' => (string) ($row->unidade_codigo ?: $row->unidade_medida ?: 'UN'),
                'sell_price' => (float) ($row->preco_venda ?? 0),
                'quantity' => (float) ($row->quantidade ?? 0),
                'location' => (string) ($row->localizacao ?? ''),
                'batch' => (string) ($row->lote ?? ''),
            ];
        })->values()->all();

        $totalQuantity = array_reduce($stock, static function (float $carry, array $row): float {
            return $carry + (float) ($row['quantity'] ?? 0);
        }, 0.0);

        return response()->json([
            'stock' => $stock,
            'total_quantity' => $totalQuantity,
        ]);
    }

    public function availability(Request $request): JsonResponse
    {
        $term = trim((string) ($request->query('search', $request->query('q', ''))));

        $rows = $this->catalogQuery($term, true)
            ->limit(50)
            ->get();

        $available = $rows->map(fn ($row): array => $this->mapCatalogRow($row))->values()->all();

        return response()->json([
            'available' => $available,
            'count' => count($available),
        ]);
    }

    public function customerByPhone(Request $request): JsonResponse
    {
        $phone = (string) $request->query('phone', '');
        $customer = $this->findCustomerByPhone($phone);

        return response()->json([
            'found' => (bool) $customer,
            'customer' => $customer ? $this->mapCustomer($customer) : null,
        ]);
    }

    public function customerSummary(int $id): JsonResponse
    {
        /** @var Cliente|null $customer */
        $customer = Cliente::query()->find($id);
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $orders = $this->customerOrdersQuery($customer)
            ->with('items')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return response()->json([
            'customer' => $this->mapCustomer($customer),
            'recent_sales' => $orders->map(fn (Order $order): array => $this->mapOrder($order))->values()->all(),
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $document = (string) $request->query('customer_cpf', '');
        $customer = $this->findCustomerByDocument($document);

        if (! $customer) {
            return response()->json([
                'found' => false,
                'orders' => [],
                'count' => 0,
            ]);
        }

        $orders = $this->customerOrdersQuery($customer)
            ->with('items')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return response()->json([
            'found' => true,
            'customer_name' => $this->resolveCustomerName($customer),
            'orders' => $orders->map(fn (Order $order): array => $this->mapOrder($order))->values()->all(),
            'count' => $orders->count(),
        ]);
    }

    public function order(int $id): JsonResponse
    {
        /** @var Order|null $order */
        $order = Order::query()->with('items')->find($id);
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json([
            'order' => $this->mapOrder($order),
        ]);
    }

    public function customerBalance(Request $request): JsonResponse
    {
        $document = (string) $request->query('cpf', '');
        $customer = $this->findCustomerByDocument($document);

        if (! $customer) {
            return response()->json([
                'found' => false,
                'credit_limit' => 0,
                'blocked' => false,
                'recent_total' => 0,
                'recent_count' => 0,
            ]);
        }

        $recentSince = now()->subDays(90);
        $ordersQuery = $this->customerOrdersQuery($customer);
        $recentTotal = (float) (clone $ordersQuery)
            ->where('created_at', '>=', $recentSince)
            ->sum('total_valor');
        $recentCount = (int) (clone $ordersQuery)
            ->where('created_at', '>=', $recentSince)
            ->count();

        return response()->json([
            'found' => true,
            'customer_name' => $this->resolveCustomerName($customer),
            'credit_limit' => (float) ($customer->limite_credito ?? 0),
            'blocked' => (bool) ($customer->bloqueado ?? false),
            'recent_total' => $recentTotal,
            'recent_count' => $recentCount,
        ]);
    }

    public function activePriceTable(): JsonResponse
    {
        $table = $this->resolveActivePriceTable();

        return response()->json([
            'found' => (bool) $table,
            'price_table' => $table ? [
                'id' => (int) $table->id,
                'code' => (string) $table->codigo,
                'name' => (string) $table->nome,
                'target_type' => (string) $table->tipo_alvo,
                'currency' => (string) ($table->moeda ?: 'BRL'),
            ] : null,
        ]);
    }

    public function quote(Request $request): JsonResponse
    {
        $items = $request->query('items', $request->input('items', []));
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            $items = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($items)) {
            $items = [];
        }

        // Protege contra payloads enormes que derrubam o banco.
        if (count($items) > 100) {
            $items = array_slice($items, 0, 100);
        }

        $activeTable = $this->resolveActivePriceTable();
        $normalizedItems = [];
        $productIds = [];
        $total = 0.0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (float) ($item['quantity'] ?? 0);
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            $normalizedItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
            $productIds[] = $productId;
        }

        $productIds = array_values(array_unique($productIds));
        /** @var \Illuminate\Support\Collection<int, Produto> $products */
        $products = $productIds !== []
            ? Produto::query()->whereIn('id_produto', $productIds)->get()->keyBy('id_produto')
            : collect();

        $lineItems = [];
        foreach ($normalizedItems as $normalizedItem) {
            $productId = $normalizedItem['product_id'];
            $quantity = $normalizedItem['quantity'];

            /** @var Produto|null $product */
            $product = $products->get($productId);
            if (! $product) {
                continue;
            }

            $unitPrice = $this->resolveQuoteUnitPrice($product, $quantity, $activeTable);
            $subtotal = round($unitPrice * $quantity, 2);
            $total += $subtotal;

            $lineItems[] = [
                'product_id' => (int) $product->id_produto,
                'product_code' => (string) $product->cod_produto,
                'product_name' => (string) $product->nome_produto,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }

        return response()->json([
            'price_table' => $activeTable ? (string) $activeTable->nome : 'padrao_estoque',
            'items' => $lineItems,
            'total' => round($total, 2),
        ]);
    }

    protected function catalogQuery(string $term, bool $onlyAvailable): QueryBuilder
    {
        $stockAgg = DB::table('estoques as e')
            ->select(
                'e.id_produto_fk',
                DB::raw('SUM(e.quantidade) as quantity'),
                DB::raw('MAX(e.preco_venda) as sell_price')
            )
            ->where('e.status', 1)
            ->groupBy('e.id_produto_fk');

        $query = DB::table('produtos as p')
            ->leftJoinSub($stockAgg, 's', fn ($join) => $join->on('s.id_produto_fk', '=', 'p.id_produto'))
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidade_medida_id')
            ->leftJoin('categoria_produtos as cp', 'cp.id_produto_fk', '=', 'p.id_produto')
            ->leftJoin('categorias as c', 'c.id_categoria', '=', 'cp.id_categoria_fk')
            ->where('p.status', 1)
            ->groupBy(
                'p.id_produto',
                'p.cod_produto',
                'p.nome_produto',
                'um.codigo',
                'p.unidade_medida',
                's.sell_price',
                's.quantity'
            )
            ->orderBy('p.nome_produto')
            ->selectRaw(
                "p.id_produto as product_id,
                p.cod_produto as product_code,
                p.nome_produto as product_name,
                COALESCE(um.codigo, p.unidade_medida, 'UN') as unit,
                MAX(c.nome_categoria) as category,
                COALESCE(s.sell_price, 0) as sell_price,
                COALESCE(s.quantity, 0) as quantity"
            );

        $term = trim($term);
        if ($term !== '') {
            $searchTerms = $this->resolveSearchTerms($term);

            $query->where(function ($where) use ($searchTerms) {
                foreach ($searchTerms as $needle) {
                    $like = '%' . $needle . '%';
                    $where->orWhereRaw('LOWER(p.nome_produto) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(p.cod_produto) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(c.nome_categoria) LIKE ?', [$like]);
                }
            });
        }

        if ($onlyAvailable) {
            $query->havingRaw('COALESCE(s.quantity, 0) > 0');
        }

        return $query;
    }

    /**
     * Expande termos com sinonimos para melhorar consultas em linguagem natural.
     *
     * @return array<int, string>
     */
    protected function resolveSearchTerms(string $term): array
    {
        $normalized = mb_strtolower(trim($term));
        if ($normalized === '') {
            return [];
        }

        $terms = [$normalized];
        $synonyms = [
            'fruta' => ['frutas', 'hortifruti', 'hortifrúti'],
            'frutas' => ['fruta', 'hortifruti', 'hortifrúti'],
            'verdura' => ['verduras', 'hortifruti', 'hortifrúti'],
            'verduras' => ['verdura', 'hortifruti', 'hortifrúti'],
            'legume' => ['legumes', 'hortifruti', 'hortifrúti'],
            'legumes' => ['legume', 'hortifruti', 'hortifrúti'],
        ];

        foreach ($synonyms as $key => $relatedTerms) {
            if (! str_contains($normalized, $key)) {
                continue;
            }

            foreach ($relatedTerms as $related) {
                $terms[] = mb_strtolower(trim($related));
            }
        }

        return array_values(array_unique(array_filter($terms, static fn (string $item): bool => $item !== '')));
    }

    protected function mapCatalogRow(object $row): array
    {
        return [
            'product_id' => (int) ($row->product_id ?? 0),
            'product_code' => (string) ($row->product_code ?? ''),
            'product_name' => (string) ($row->product_name ?? ''),
            'unit' => (string) ($row->unit ?? 'UN'),
            'category' => (string) ($row->category ?? ''),
            'sell_price' => (float) ($row->sell_price ?? 0),
            'quantity' => (float) ($row->quantity ?? 0),
        ];
    }

    protected function findCustomerByPhone(string $phone): ?Cliente
    {
        $phone = trim($phone);
        $digits = $this->normalizeDigits($phone);
        if ($phone === '' && $digits === '') {
            return null;
        }

        return Cliente::query()
            ->where(function ($query) use ($phone, $digits) {
                if ($phone !== '') {
                    $query->where('whatsapp', $phone)
                        ->orWhere('telefone', $phone);
                }

                if ($digits !== '') {
                    $query->orWhereRaw($this->normalizePhoneExpression('whatsapp') . ' = ?', [$digits])
                        ->orWhereRaw($this->normalizePhoneExpression('telefone') . ' = ?', [$digits]);
                }
            })
            ->orderByDesc('id_cliente')
            ->first();
    }

    protected function findCustomerByDocument(string $document): ?Cliente
    {
        $document = trim($document);
        $digits = $this->normalizeDigits($document);
        if ($document === '' && $digits === '') {
            return null;
        }

        return Cliente::query()
            ->where(function ($query) use ($document, $digits) {
                if ($document !== '') {
                    $query->where('documento', $document);
                }

                if ($digits !== '') {
                    $query->orWhereRaw($this->normalizePhoneExpression('documento') . ' = ?', [$digits]);
                }
            })
            ->orderByDesc('id_cliente')
            ->first();
    }

    protected function mapCustomer(Cliente $customer): array
    {
        return [
            'id' => (int) $customer->id_cliente,
            'name' => $this->resolveCustomerName($customer),
            'document' => (string) ($customer->documento ?? ''),
            'phone' => (string) ($customer->whatsapp ?: $customer->telefone ?: ''),
            'email' => (string) ($customer->email ?? ''),
            'blocked' => (bool) ($customer->bloqueado ?? false),
            'credit_limit' => (float) ($customer->limite_credito ?? 0),
        ];
    }

    protected function resolveCustomerName(Cliente $customer): string
    {
        $name = trim((string) ($customer->nome_fantasia ?: $customer->razao_social ?: $customer->nome ?: ''));

        return $name !== '' ? $name : 'Cliente';
    }

    protected function customerOrdersQuery(Cliente $customer): EloquentBuilder
    {
        $query = Order::query();

        // Assumimos que whatsapp/telefone já são persistidos apenas com dígitos.
        // Mesmo assim, normalizamos a entrada para manter robustez.
        $digits = collect([
            $customer->whatsapp,
            $customer->telefone,
        ])
            ->filter(fn ($value): bool => trim((string) $value) !== '')
            ->map(fn ($value): string => $this->normalizeDigits((string) $value))
            ->filter(fn (string $value): bool => $value !== '')
            ->unique()
            ->values();

        if ($digits->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        $query->whereIn('client', $digits->all());

        return $query;
    }

    protected function mapOrder(Order $order): array
    {
        $items = [];

        if ($order->relationLoaded('items')) {
            $items = $order->items
                ->map(fn ($item): array => [
                    'product_code' => (string) ($item->cod_produto ?? ''),
                    'product_name' => (string) ($item->nome_produto ?? ''),
                    'quantity' => (float) ($item->quantidade ?? 0),
                    'unit_price' => (float) ($item->preco_unit ?? 0),
                    'subtotal' => (float) ($item->sub_valor ?? 0),
                ])
                ->values()
                ->all();
        }

        return [
            'order_id' => (int) $order->id,
            'status' => (string) $order->status,
            'total' => (float) ($order->total_valor ?? 0),
            'created_at' => $order->created_at ? $order->created_at->toISOString() : null,
            'items' => $items,
        ];
    }

    protected function resolveActivePriceTable(): ?TabelaPreco
    {
        $today = now()->toDateString();

        return TabelaPreco::query()
            ->where('ativo', true)
            ->where(function ($query) use ($today) {
                $query->whereNull('inicio_vigencia')
                    ->orWhereDate('inicio_vigencia', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('fim_vigencia')
                    ->orWhereDate('fim_vigencia', '>=', $today);
            })
            ->orderByDesc('inicio_vigencia')
            ->first();
    }

    protected function resolveQuoteUnitPrice(Produto $product, float $quantity, ?TabelaPreco $activeTable): float
    {
        $basePrice = (float) (DB::table('estoques')
            ->where('status', 1)
            ->where('id_produto_fk', $product->id_produto)
            ->max('preco_venda') ?? 0);

        if (! $activeTable) {
            return round($basePrice, 2);
        }

        $pricingRow = DB::table('tabela_preco_itens')
            ->where('tabela_preco_id', $activeTable->id)
            ->where('produto_id', $product->id_produto)
            ->whereNull('marca_id')
            ->whereNull('fornecedor_id')
            ->first();

        if (! $pricingRow && ! empty($product->item_id)) {
            $pricingRow = DB::table('tabela_preco_itens')
                ->where('tabela_preco_id', $activeTable->id)
                ->where('item_id', $product->item_id)
                ->whereNull('marca_id')
                ->whereNull('fornecedor_id')
                ->first();
        }

        if (! $pricingRow) {
            return round($basePrice, 2);
        }

        $price = (float) ($pricingRow->preco ?? $basePrice);
        $minimumQty = max(1, (int) ($pricingRow->quantidade_minima ?? 1));
        $discount = (float) ($pricingRow->desconto_percent ?? 0);

        if ($discount > 0 && $quantity >= $minimumQty) {
            $price *= (1 - ($discount / 100));
        }

        return round($price, 2);
    }

    protected function normalizeDigits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    protected function normalizePhoneExpression(string $column): string
    {
        // O banco armazena apenas dígitos, então não precisamos "limpar" via SQL.
        return $column;
    }
}
