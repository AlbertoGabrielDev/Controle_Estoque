<?php

namespace App\Http\Controllers;

use App\Enums\Canal;
use App\Enums\Scope;
use App\Enums\TipoOperacao;
use App\Enums\UF;
use App\Models\Categoria;
use App\Models\Tax;
use App\Models\TaxRule;
use App\Models\CustomerSegment;
use App\Models\ProductSegment;
use App\Repositories\TaxRuleRepository;
use App\Services\DataTableService;
use App\Http\Requests\TaxRuleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Inertia\Inertia;
use App\Enums\TaxMethod;

class TaxRuleController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private TaxRuleRepository $rules
    ) {
    }


    public function index(Request $request)
    {
        return Inertia::render('Taxes/Index');
    }

    public function data(Request $request)
    {

        [$query, $columnsMap] = TaxRule::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $editBtn = Blade::render(
                        '<x-edit-button :route="$route" :model-id="$id" />',
                        ['route' => 'taxes.edit', 'id' => $row->id]
                    );

                    // excluir (form simples com csrf/method delete)
                    $deleteUrl = route('taxes.destroy', $row->id);
                    $deleteBtn = Blade::render(<<<'BLADE'
                        <form method="POST" action="{{ $action }}" onsubmit="return confirm('Excluir esta regra?');" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-2 py-1 text-sm rounded bg-red-50 hover:bg-red-100 text-red-700">
                                Excluir
                            </button>
                        </form>
                    BLADE, ['action' => $deleteUrl]);

                    $html = trim($editBtn . $deleteBtn);
                    if ($html === '') {
                        $html = '<span class="inline-block w-8 h-8 opacity-0" aria-hidden="true">&nbsp;</span>';
                    }
                    return '<div class="flex gap-2 justify-start items-center">' . $html . '</div>';
                });
            }
        );
    }

    public function create()
    {
        // valores padrão para o form
        $defaultRule = [
            'escopo' => Scope::Item->value,
            'prioridade' => 100,
            'vigencia_inicio' => null,
            'vigencia_fim' => null,
            'uf_origem' => null,
            'uf_destino' => null,
            'segment_id' => null,
            'categoria_produto_id' => null,
            'base_formula' => 'valor_menos_desc',
            'product_segment_ids' => [],
            'metodo' => 1,
            'aliquota_percent' => null,
            'valor_fixo' => null,
            'expression' => null,
            'cumulativo' => false,
            'tipo_operacao' => null,
            'canal' => null,
        ];

        return Inertia::render('Taxes/Create', array_merge([
            'ufs' => UF::cases(),
            'customerSegments' => CustomerSegment::select('id', 'nome')->orderBy('nome')->get(),
            'productSegments' => Categoria::select('id_categoria', 'nome_categoria')->orderBy('nome_categoria')->get(),
            'taxes' => ['codigo' => '', 'nome' => ''],
            'rule' => $defaultRule,
        ], $this->enumSelectOptions()));
    }

    public function store(TaxRuleRequest $request)
    {
        $payload = $this->mapFormToDb($request->validated());
        unset($payload['categoria_produto_id']);
        $rule = TaxRule::create($payload);
        $catIds = collect($request->input('product_segment_ids', []))
            ->filter()->map(fn($v) => (int) $v)->unique()->values()->all();
        $rule->categorias()->sync($catIds);
        return redirect()
            ->route('taxes.edit', $rule->id)
            ->with('success', 'Regra de taxa criada com sucesso.');
    }

    public function update(TaxRuleRequest $request, TaxRule $rule)
    {
        $payload = $this->mapFormToDb($request->validated());
        unset($payload['categoria_produto_id']);

        $rule->update($payload);

        $catIds = collect($request->input('product_segment_ids', []))
            ->filter()->map(fn($v) => (int) $v)->unique()->values()->all();

        $rule->categorias()->sync($catIds);

        return redirect()
            ->route('taxes.index')
            ->with('success', 'Regra atualizada com sucesso.');
    }

    private function mapFormToDb(array $data): array
    {
        $out = [];
        $out['tax_id'] = $this->resolveTaxId($data);
        $out['escopo'] = (int) ($data['scope'] ?? Scope::Item->value);
        $out['base_formula'] = $this->uiBaseToDb($data['base'] ?? 'price');
        $out['metodo'] = $this->uiMethodToInt($data['method'] ?? 'percent');
        $out['cumulativo'] = (($data['apply_mode'] ?? 'stack') === 'stack');
        $out['prioridade'] = (int) ($data['priority'] ?? 100);
        $out['vigencia_inicio'] = $data['starts_at'] ?? null;
        $out['vigencia_fim'] = $data['ends_at'] ?? null;

        $normalize = fn($v) => ($v === '' ? null : $v);
        $out['uf_origem'] = $normalize($data['origin_uf'] ?? null);
        $out['uf_destino'] = $normalize($data['dest_uf'] ?? null);
        $out['segment_id'] = $normalize($data['customer_segment_id'] ?? null);
        // $out['categoria_produto_id'] = $normalize($data['product_segment_id'] ?? null); // ❌ REMOVER
        $out['ncm_padrao'] = $normalize($data['ncm'] ?? null);
        $out['canal'] = $normalize($data['canal'] ?? null);
        $out['tipo_operacao'] = $normalize($data['tipo_operacao'] ?? null);

        $out['aliquota_percent'] = null;
        $out['valor_fixo'] = null;
        $out['expression'] = null;

        switch ($out['metodo']) {
            case TaxMethod::Percent->value:
                $out['aliquota_percent'] = $data['rate'] ?? null;
                break;
            case TaxMethod::Fixed->value:
                $out['valor_fixo'] = $data['amount'] ?? null;
                break;
            case TaxMethod::Formula->value:
                $out['expression'] = $data['formula'] ?? null;
                if (!empty($data['rate']))
                    $out['aliquota_percent'] = $data['rate'];
                break;
        }

        return $out;
    }
    public function edit(TaxRule $rule)
    {
        $rule->load([
            'alvos:id,tax_rule_id,id_categoria_fk,id_produto_fk',
            'alvosCategorias:id,tax_rule_id,id_categoria_fk',
            'alvosProdutos:id,tax_rule_id,id_produto_fk',
            'tax:id,codigo,nome',
        ]);

        $metodoVal = is_object($rule->metodo) ? $rule->metodo->value : (int) $rule->metodo;
        $escopoVal = is_object($rule->escopo) ? $rule->escopo->value : (int) $rule->escopo;

        // 👇 agora é um ARRAY de categorias
        $categoriaIds = $rule->alvosCategorias->pluck('id_categoria_fk')->filter()->values();

        $ruleUi = [
            'id' => $rule->id,
            'escopo' => $escopoVal,
            'prioridade' => (int) $rule->prioridade,
            'vigencia_inicio' => optional($rule->vigencia_inicio)->format('Y-m-d'),
            'vigencia_fim' => optional($rule->vigencia_fim)->format('Y-m-d'),
            'uf_origem' => $rule->uf_origem,
            'uf_destino' => $rule->uf_destino,
            'segment_id' => $rule->segment_id,
            // ⚠️ não use mais 'categoria_produto_id' único
            'product_segment_ids' => $categoriaIds, // 👈 múltiplas categorias selecionadas
            'base_formula' => $rule->base_formula,
            'metodo' => $metodoVal,
            'aliquota_percent' => $rule->aliquota_percent,
            'valor_fixo' => $rule->valor_fixo,
            'expression' => $rule->expression,
            'rate' => $rule->aliquota_percent,
            'amount' => $rule->valor_fixo,
            'formula' => $rule->expression,
            'cumulativo' => (bool) $rule->cumulativo,
            'canal' => $rule->canal?->value,
            'tipo_operacao' => $rule->tipo_operacao?->value,
        ];

        $tax = $rule->tax()->select('id', 'codigo', 'nome')->first();

        return Inertia::render('Taxes/Edit', array_merge([
            'taxes' => $tax ?: ['codigo' => '', 'nome' => ''],
            'rule' => $ruleUi,
            'ufs' => UF::cases(),
            'customerSegments' => CustomerSegment::select('id', 'nome')->orderBy('nome')->get(),
            'productSegments' => Categoria::select('id_categoria', 'nome_categoria')->orderBy('nome_categoria')->get(),
        ], $this->enumSelectOptions()));
    }

    private function enumSelectOptions(): array
    {
        return [
            'channels' => Canal::options(),
            'operationTypes' => TipoOperacao::options(),
        ];
    }

    private function resolveTaxId(array $data): int
    {
        $code = trim($data['tax_code'] ?? '');
        $name = trim($data['name'] ?? $code);
        if ($code === '') {
            abort(422, 'Código do imposto (tax_code) é obrigatório.');
        }
        $tax = Tax::firstOrCreate(['codigo' => $code], [
            'nome' => $name ?: $code,
            'ativo' => true,
        ]);
        if ($name && $tax->nome !== $name) {
            $tax->nome = $name;
            $tax->save();
        }
        return $tax->id;
    }

    private function uiBaseToDb(string $base): string
    {
        return match ($base) {
            'price' => 'valor_menos_desc',
            'price+freight' => 'valor_mais_frete',
            'subtotal' => 'valor',
            default => 'valor_menos_desc',
        };
    }

    public function destroy(TaxRule $rule)
    {
        $this->rules->delete($rule->id);
        return redirect()
            ->route('taxes.index')
            ->with('success', 'Regra de taxa excluída.');
    }

    private function uiMethodToInt(string $method): int
    {
        return match ($method) {
            'percent' => TaxMethod::Percent->value,
            'fixed' => TaxMethod::Fixed->value,
            'formula' => TaxMethod::Formula->value,
            default => TaxMethod::Percent->value,
        };
    }
}
