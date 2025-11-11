<script setup>
import { Link } from '@inertiajs/vue3'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import { computed } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  ufs: { type: Array, default: () => [] },
  customerSegments: { type: Array, default: () => [] },
  productSegments: { type: Array, default: () => [] },
  channels: { type: Array, default: () => [] },
  operationTypes: { type: Array, default: () => [] },
})

defineEmits(['submit'])

const hints = {
  name: 'Nome amigável da regra (uso interno).',
  tax_code: 'Código curto do imposto (ICMS, PIS, COFINS, etc).',
  scope: '1=Item, 2=Frete, 3=Pedido.',
  priority: 'Menor número roda antes dentro do mesmo imposto.',
  starts_at: 'Início de vigência (inclusive).',
  ends_at: 'Fim de vigência (inclusive).',
  origin_uf: 'UF de origem. Vazio = qualquer.',
  dest_uf: 'UF de destino. Vazio = qualquer.',
  customer_segment_id: 'Segmento do cliente. Vazio = todos.',
  product_segment_id: 'Categorias do produto (múltiplas).',
  base: 'Base de cálculo da regra.',
  method: 'Percentual, Valor Fixo ou Fórmula.',
  rate: 'Alíquota percentual.',
  amount: 'Valor fixo (R$).',
  formula: 'Expressão (usa variáveis como base e rate).',
  apply_mode: 'Cumulativa (soma) ou Exclusiva (inibe outras).',
  canal: 'Canal (balcao, ecommerce, etc).',
  tipo_operacao: 'Natureza da operação.',
}

// Opções formatadas para o vue-multiselect
const productOptions = computed(() =>
  (props.productSegments || []).map(s => ({
    value: Number(s.id_categoria),
    label: s.nome_categoria,
  }))
)
// Garante que o v-model seja sempre array numérico
// function ensureArrayNumbers(arr) {
//   if (!Array.isArray(arr)) return []
//   return arr.map(v => Number(v)).filter(v => !Number.isNaN(v))
// }
// props.form.product_segment_ids = ensureArrayNumbers(props.form.product_segment_ids ?? [])
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Nome + Código -->
    <div class="col-span-1 md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Nome <span class="ml-1 text-gray-400 cursor-help" :title="hints.name">ⓘ</span>
      </label>
      <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors?.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Código (ex.: ICMS)
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.tax_code">ⓘ</span>
      </label>
      <input v-model="form.tax_code" type="text" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors?.tax_code" class="text-sm text-red-600 mt-1">{{ form.errors.tax_code }}</div>
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
      <div v-if="form.errors?.scope" class="text-sm text-red-600 mt-1">{{ form.errors.scope }}</div>
    </div>

    <!-- Prioridade + Período -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Prioridade
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.priority">ⓘ</span>
      </label>
      <input v-model.number="form.priority" type="number" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors?.priority" class="text-sm text-red-600 mt-1">{{ form.errors.priority }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Início <span class="ml-1 text-gray-400 cursor-help" :title="hints.starts_at">ⓘ</span>
      </label>
      <input v-model="form.starts_at" type="date" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors?.starts_at" class="text-sm text-red-600 mt-1">{{ form.errors.starts_at }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Fim <span class="ml-1 text-gray-400 cursor-help" :title="hints.ends_at">ⓘ</span>
      </label>
      <input v-model="form.ends_at" type="date" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors?.ends_at" class="text-sm text-red-600 mt-1">{{ form.errors.ends_at }}</div>
    </div>

    <!-- Filtros -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        UF Origem <span class="ml-1 text-gray-400 cursor-help" :title="hints.origin_uf">ⓘ</span>
      </label>
      <select v-model="form.origin_uf" class="w-full border rounded px-3 py-2">
        <option value="">—</option>
        <option v-for="u in ufs" :key="u" :value="u">{{ u }}</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        UF Destino <span class="ml-1 text-gray-400 cursor-help" :title="hints.dest_uf">ⓘ</span>
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

    <!-- Escopo ITEM → categorias múltiplas (vue-multiselect) -->
    <div v-if="form.scope === 1" class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Categorias do Produto (múltiplas)
        <span class="ml-1 text-gray-400 cursor-help" :title="hints.product_segment_id">ⓘ</span>
      </label>

      <Multiselect v-model="form.product_segment_ids" :options="productOptions" :multiple="true"
        :close-on-select="false" :clear-on-select="false" :preserve-search="true" :searchable="true"
        :show-no-results="true" placeholder="Selecione categorias…" label="label" track-by="value" />

      <p class="text-xs text-gray-500 mt-1">Dica: pesquise e selecione várias categorias.</p>

      <div v-if="form.errors?.product_segment_ids" class="text-sm text-red-600 mt-1">
        {{ form.errors.product_segment_ids }}
      </div>
    </div>

    <!-- Base -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Base <span class="ml-1 text-gray-400 cursor-help" :title="hints.base">ⓘ</span>
      </label>
      <select v-model="form.base" class="w-full border rounded px-3 py-2">
        <option value="price">Preço (itens líquidos, sem frete)</option>
        <option value="price+freight">Preço + Frete</option>
        <option value="subtotal">Subtotal</option>
      </select>
      <div v-if="form.errors?.base" class="text-sm text-red-600 mt-1">{{ form.errors.base }}</div>
    </div>

    <!-- Método -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Método <span class="ml-1 text-gray-400 cursor-help" :title="hints.method">ⓘ</span>
      </label>
      <select v-model="form.method" class="w-full border rounded px-3 py-2">
        <option value="percent">Percentual (%)</option>
        <option value="fixed">Valor Fixo (R$)</option>
        <option value="formula">Fórmula</option>
      </select>
      <div v-if="form.errors?.method" class="text-sm text-red-600 mt-1">{{ form.errors.method }}</div>
    </div>

    <!-- Campos variáveis conforme método -->
    <div v-if="form.method === 'percent'">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Alíquota (%) <span class="ml-1 text-gray-400 cursor-help" :title="hints.rate">ⓘ</span>
      </label>
      <input v-model="form.rate" type="number" step="0.0001" min="0" class="w-full border rounded px-3 py-2"
        @input="form.rate = form.rate === '' ? null : form.rate" />
      <div v-if="form.errors?.rate" class="text-sm text-red-600 mt-1">{{ form.errors.rate }}</div>
    </div>

    <div v-if="form.method === 'fixed'">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Valor Fixo <span class="ml-1 text-gray-400 cursor-help" :title="hints.amount">ⓘ</span>
      </label>
      <input v-model.number="form.amount" type="number" step="0.01" class="w-full border rounded px-3 py-2" />
      <div v-if="form.errors?.amount" class="text-sm text-red-600 mt-1">{{ form.errors.amount }}</div>
    </div>

    <div v-if="form.method === 'formula'" class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Fórmula <span class="ml-1 text-gray-400 cursor-help" :title="hints.formula">ⓘ</span>
      </label>
      <input v-model="form.formula" type="text" class="w-full border rounded px-3 py-2 font-mono"
        placeholder="ex.: base * (rate/100)" />
      <div v-if="form.errors?.formula" class="text-sm text-red-600 mt-1">{{ form.errors.formula }}</div>
    </div>

    <!-- Canal / Tipo de Operação -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Canal <span class="ml-1 text-gray-400 cursor-help" :title="hints.canal">ⓘ</span>
      </label>
      <select v-model="form.canal" class="w-full border rounded px-3 py-2">
        <option value="">—</option>
        <option v-for="c in channels" :key="c.value" :value="c.value">{{ c.label }}</option>
      </select>
      <div v-if="form.errors?.canal" class="text-sm text-red-600 mt-1">{{ form.errors.canal }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Tipo de Operação <span class="ml-1 text-gray-400 cursor-help" :title="hints.tipo_operacao">ⓘ</span>
      </label>
      <select v-model="form.tipo_operacao" class="w-full border rounded px-3 py-2">
        <option value="">—</option>
        <option v-for="op in operationTypes" :key="op.value" :value="op.value">{{ op.label }}</option>
      </select>
      <div v-if="form.errors?.tipo_operacao" class="text-sm text-red-600 mt-1">{{ form.errors.tipo_operacao }}</div>
    </div>

    <!-- Modo de aplicação -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Modo de Aplicação <span class="ml-1 text-gray-400 cursor-help" :title="hints.apply_mode">ⓘ</span>
      </label>
      <select v-model="form.apply_mode" class="w-full border rounded px-3 py-2">
        <option value="stack">Cumulativa (soma)</option>
        <option value="exclusive">Exclusiva (inibe outras)</option>
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

<style scoped>
/* ajuste fino para casar com seu tailwind/form */
.multiselect--tw :deep(.multiselect) {
  min-height: 42px;
}

.multiselect--tw :deep(.multiselect__tags) {
  min-height: 42px;
  padding: 6px 40px 6px 8px;
}
</style>
