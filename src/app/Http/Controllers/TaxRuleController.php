<?php

namespace App\Http\Controllers;

use App\Enums\Scope;
use App\Enums\UF;
use App\Models\Categoria;
use App\Models\Tax;
use App\Models\TaxRule;
use App\Models\CustomerSegment;
use App\Models\ProductSegment; // remova se não usar
use App\Repositories\TaxRuleRepository;
use App\Services\DataTableService;
use App\Http\Requests\TaxRuleRequest; // troque por Request se não tiver FormRequest
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Inertia\Inertia;
use App\Enums\TaxMethod;  // enum int: 1=Percent, 2=Fixed, 3=Formula

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
                    // editar
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
            'metodo' => 1, // Percent
            'aliquota_percent' => null,
            'valor_fixo' => null,
            'expression' => null,
            'cumulativo' => false,
        ];

        return Inertia::render('Taxes/Create', [
            'ufs' => UF::cases(),
            'customerSegments' => CustomerSegment::select('id', 'nome')->orderBy('nome')->get(),
            'productSegments' => Categoria::select('id_categoria', 'nome_categoria')->orderBy('nome_categoria')->get(),
            'taxes' => ['codigo' => '', 'nome' => ''],
            'rule' => $defaultRule,
        ]);
    }

    public function store(TaxRuleRequest $request)
    {
        $payload = $this->mapFormToDb($request->validated());

        // Cria a regra
        $rule = TaxRule::create($payload);
        // Se você usa repositório: $rule = $this->rules->create($payload);

        return redirect()
            ->route('taxes.edit', $rule->id)
            ->with('success', 'Regra de taxa criada com sucesso.');
    }

    public function update(TaxRuleRequest $request, TaxRule $rule)
    {
        $payload = $this->mapFormToDb($request->validated());
        $rule->update($payload);

        return redirect()
            ->route('taxes.index')                 // <<< volta para a lista
            ->with('success', 'Regra atualizada com sucesso.'); // <<< flash para o toast
    }

    /**
     * Converte os campos do formulário (UI) para as colunas do banco (tax_rules).
     */
    private function mapFormToDb(array $data): array
    {
        $out = [];

        // FK do imposto (cria/atualiza em taxes se necessário)
        $out['tax_id'] = $this->resolveTaxId($data);

        // Escopo numérico (1=Item, 2=Frete, 3=Pedido)
        $out['escopo'] = (int) ($data['scope'] ?? Scope::Item->value);

        // Base (UI -> enum do banco)
        $out['base_formula'] = $this->uiBaseToDb($data['base'] ?? 'price');

        // Método (UI -> int)
        $out['metodo'] = $this->uiMethodToInt($data['method'] ?? 'percent');

        // Modo de aplicação
        $out['cumulativo'] = (($data['apply_mode'] ?? 'stack') === 'stack');

        // Prioridade
        $out['prioridade'] = (int) ($data['priority'] ?? 100);

        // Vigência
        $out['vigencia_inicio'] = $data['starts_at'] ?? null;
        $out['vigencia_fim'] = $data['ends_at'] ?? null;

        // Filtros
        $out['uf_origem'] = $data['origin_uf'] ?? null;
        $out['uf_destino'] = $data['dest_uf'] ?? null;
        $out['segment_id'] = $data['customer_segment_id'] ?: null;
        $out['categoria_produto_id'] = $data['product_segment_id'] ?: null;
        $out['ncm_padrao'] = $data['ncm'] ?? null; // se não vier da UI, ficará null

        // Limpamos sempre e setamos só o que o método usa
        $out['aliquota_percent'] = null;
        $out['valor_fixo'] = null;
        $out['expression'] = null;

        switch ($out['metodo']) {
            case TaxMethod::Percent->value:
                // Percentual: usa aliquota_percent
                $out['aliquota_percent'] = $data['rate'] ?? null;
                break;

            case TaxMethod::Fixed->value:
                // Valor fixo: usa valor_fixo
                $out['valor_fixo'] = $data['amount'] ?? null;
                break;

            case TaxMethod::Formula->value:
                // Fórmula: usa expression; opcionalmente aceita 'rate' como variável
                $out['expression'] = $data['formula'] ?? null;
                if (!empty($data['rate'])) {
                    $out['aliquota_percent'] = $data['rate'];
                }
                break;
        }

        return $out;
    }
    public function edit(TaxRule $rule)
    {
        // normaliza valores para a UI
        $metodoVal = is_object($rule->metodo) ? $rule->metodo->value : (int) $rule->metodo;
        $escopoVal = is_object($rule->escopo) ? $rule->escopo->value : (int) $rule->escopo;

        $ruleUi = [
            'id' => $rule->id,
            'escopo' => $escopoVal,
            'prioridade' => (int) $rule->prioridade,
            'vigencia_inicio' => optional($rule->vigencia_inicio)->format('Y-m-d'),
            'vigencia_fim' => optional($rule->vigencia_fim)->format('Y-m-d'),
            'uf_origem' => $rule->uf_origem,
            'uf_destino' => $rule->uf_destino,
            'segment_id' => $rule->segment_id,
            'categoria_produto_id' => $rule->categoria_produto_id,

            // o Edit.vue converte DB->UI, então envie o valor do DB aqui:
            'base_formula' => $rule->base_formula,
            'metodo' => $metodoVal,              // <<< numérico 1/2/3

            // equivalentes para preencher os inputs
            'aliquota_percent' => $rule->aliquota_percent,
            'valor_fixo' => $rule->valor_fixo,
            'expression' => $rule->expression,

            // extras que o form usa diretamente
            'rate' => $rule->aliquota_percent,
            'amount' => $rule->valor_fixo,
            'formula' => $rule->expression,

            'cumulativo' => (bool) $rule->cumulativo,
        ];

        $tax = $rule->tax()->select('id', 'codigo', 'nome')->first();

        return Inertia::render('Taxes/Edit', [
            'taxes' => $tax ?: ['codigo' => '', 'nome' => ''],
            'rule' => $ruleUi,
            'ufs' => UF::cases(),
            'customerSegments' => CustomerSegment::select('id', 'nome')->orderBy('nome')->get(),
            'productSegments' => Categoria::select('id_categoria', 'nome_categoria')->orderBy('nome_categoria')->get(),
        ]);
    }
    /**
     * Garante que exista um Tax correspondente ao tax_code/name e retorna seu ID.
     */
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

        // Se o nome veio diferente, atualiza
        if ($name && $tax->nome !== $name) {
            $tax->nome = $name;
            $tax->save();
        }

        return $tax->id;
    }

    /**
     * Converte a base da UI para o enum do banco.
     * UI: price | price+freight | subtotal
     * DB: valor_menos_desc | valor_mais_frete | valor
     */
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

    /**
     * Converte o método da UI para o inteiro do banco.
     * UI: percent | fixed | formula
     * DB: 1 | 2 | 3  (TaxMethod)
     */
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
