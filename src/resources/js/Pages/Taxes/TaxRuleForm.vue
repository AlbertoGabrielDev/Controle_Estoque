<script setup>
const props = defineProps({
  form: { type: Object, required: true },
  ufs: { type: Array, default: () => [] },
  customerSegments: { type: Array, default: () => [] },
  productSegments: { type: Array, default: () => [] },
})
const emit = defineEmits(['submit'])

// TEXTOS DE AJUDA (tooltips)
const hints = {
  name: 'Nome amigável da regra. Ex.: "ICMS GO interno – Vestuário (19%)". Não afeta o cálculo.',
  tax_code: 'Código curto do imposto a que esta regra pertence. Ex.: ICMS, ICMS_ST, DIFAL, FCP, PIS, COFINS.',
  scope: 'Onde a regra aplica: 1=Item (por produto), 2=Frete (valor do frete), 3=Pedido (uma vez no total, ex.: DIFAL).',
  priority: 'Ordem de execução dentro do mesmo imposto+escopo. Menor número roda antes.',
  starts_at: 'Data inicial de vigência (inclusive). Em branco = já vale.',
  ends_at: 'Data final de vigência (inclusive). Em branco = sem fim.',
  origin_uf: 'UF de origem (remetente/saída). Em branco = qualquer.',
  dest_uf: 'UF de destino (destinatário/entrega). Em branco = qualquer.',
  customer_segment_id: 'Segmento do cliente (ex.: Varejo, Atacado). Vazio = todos.',
  product_segment_id: 'Categoria/segmento do produto (ex.: Bebidas, Hortifruti). Só faz sentido quando Escopo = Item.',
  base: 'Base de cálculo do imposto: "Preço" = itens líquidos (sem frete); "Preço + Frete" = itens líquidos + frete cobrado; "Subtotal" = usa um subtotal já pronto do seu domínio.',
  method: 'Como calcular: Percentual(%) usa alíquota; Valor Fixo (R$) aplica um valor constante; Fórmula executa uma expressão.',
  rate: 'Alíquota percentual da regra (ex.: 19). Usada em "Percentual" e pode ser variável "rate" na Fórmula.',
  amount: 'Valor fixo da regra (em R$). Usado quando o método = Valor Fixo.',
  formula: 'Expressão de cálculo quando método = Fórmula. Variáveis usuais: base, rate, freight, uf_origem, uf_destino.',
  apply_mode: 'Como combinar com outras regras do mesmo imposto+escopo: "Cumulativa" soma; "Exclusiva" inibe as demais.',
}
</script>

<template>
  <form @submit.prevent="emit('submit')" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Básico -->
    <div class="col-span-1 md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Nome
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.name">ⓘ</span>
      </label>
      <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Código (ex.: ICMS)
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.tax_code">ⓘ</span>
      </label>
      <input v-model="form.tax_code" type="text" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors.tax_code" class="text-sm text-red-600 mt-1">{{ form.errors.tax_code }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Escopo
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.scope">ⓘ</span>
      </label>
      <select v-model.number="form.scope" class="w-full border rounded px-3 py-2">
        <option :value="1">Item</option>
        <option :value="2">Frete</option>
        <option :value="3">Pedido</option>
      </select>
      <div v-if="form.errors.scope" class="text-sm text-red-600 mt-1">{{ form.errors.scope }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Prioridade
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.priority">ⓘ</span>
      </label>
      <input v-model.number="form.priority" type="number" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors.priority" class="text-sm text-red-600 mt-1">{{ form.errors.priority }}</div>
    </div>

    <!-- Período -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Início
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.starts_at">ⓘ</span>
      </label>
      <input v-model="form.starts_at" type="date" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors.starts_at" class="text-sm text-red-600 mt-1">{{ form.errors.starts_at }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Fim
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.ends_at">ⓘ</span>
      </label>
      <input v-model="form.ends_at" type="date" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors.ends_at" class="text-sm text-red-600 mt-1">{{ form.errors.ends_at }}</div>
    </div>

    <!-- Filtros -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        UF Origem
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.origin_uf">ⓘ</span>
      </label>
      <select v-model="form.origin_uf" class="w-full border rounded px-3 py-2">
        <option value="">—</option>
        <option v-for="u in ufs" :key="u" :value="u">{{ u }}</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        UF Destino
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.dest_uf">ⓘ</span>
      </label>
      <select v-model="form.dest_uf" class="w-full border rounded px-3 py-2">
        <option value="">—</option>
        <option v-for="u in ufs" :key="u" :value="u">{{ u }}</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Segmento do Cliente
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.customer_segment_id">ⓘ</span>
      </label>
      <select v-model="form.customer_segment_id" class="w-full border rounded px-3 py-2">
        <option value="">—</option>
        <option v-for="s in customerSegments" :key="s.id" :value="s.id">{{ s.nome }}</option>
      </select>
    </div>

    <!-- Escopo ITEM: mostrar categoria -->
    <div v-if="form.scope === 1">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Segmento do Produto
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.product_segment_id">ⓘ</span>
      </label>
      <select v-model="form.product_segment_id" class="w-full border rounded px-3 py-2">
        <option value="">—</option>
        <option v-for="s in productSegments" :key="s.id_categoria" :value="s.id_categoria">
          {{ s.nome_categoria }}
        </option>
      </select>
    </div>

    <!-- Cálculo -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Base
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.base">ⓘ</span>
      </label>
      <select v-model="form.base" class="w-full border rounded px-3 py-2">
        <option value="price">Preço (itens líquidos, sem frete)</option>
        <option value="price+freight">Preço + Frete (itens líquidos + frete)</option>
        <option value="subtotal">Subtotal (valor já consolidado)</option>
      </select>
      <div v-if="form.errors.base" class="text-sm text-red-600 mt-1">{{ form.errors.base }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Método
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.method">ⓘ</span>
      </label>
      <select v-model="form.method" class="w-full border rounded px-3 py-2">
        <option value="percent">Percentual (%)</option>
        <option value="fixed">Valor Fixo (R$)</option>
        <option value="formula">Fórmula (expressão)</option>
      </select>
      <div v-if="form.errors.method" class="text-sm text-red-600 mt-1">{{ form.errors.method }}</div>
    </div>

    <div v-if="form.method === 'percent'">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Alíquota (%)
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.rate">ⓘ</span>
      </label>
      <input v-model.number="form.rate" type="number" step="0.01" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors.rate" class="text-sm text-red-600 mt-1">{{ form.errors.rate }}</div>
    </div>

    <div v-if="form.method === 'fixed'">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Valor Fixo
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.amount">ⓘ</span>
      </label>
      <input v-model.number="form.amount" type="number" step="0.01" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors.amount" class="text-sm text-red-600 mt-1">{{ form.errors.amount }}</div>
    </div>

    <div v-if="form.method === 'formula'" class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Fórmula
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.formula">ⓘ</span>
      </label>
      <input v-model="form.formula" type="text" class="w-full border rounded px-3 py-2 font-mono"
        placeholder="ex.: base * (rate/100)" />
      <p class="text-xs text-gray-500 mt-1">
        Variáveis comuns: <code>base</code>, <code>rate</code>, <code>freight</code>, <code>uf_origem</code>,
        <code>uf_destino</code>.
      </p>
      <div v-if="form.errors.formula" class="text-sm text-red-600 mt-1">{{ form.errors.formula }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Modo de Aplicação
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.apply_mode">ⓘ</span>
      </label>
      <select v-model="form.apply_mode" class="w-full border rounded px-3 py-2">
        <option value="stack">Cumulativa (soma com outras da mesma família)</option>
        <option value="exclusive">Exclusiva (inibe outras da mesma família)</option>
      </select>
    </div>

    <!-- Ações -->
    <div class="md:col-span-2 flex justify-end gap-2 mt-4">
      <Link :href="route('taxes.index')" class="px-3 py-2 rounded bg-gray-100">Cancelar</Link>
      <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white" :disabled="form.processing">
        {{ form.processing ? 'Salvando...' : 'Salvar' }}
      </button>
    </div>
  </form>
</template>
