<script setup>
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import { computed, ref, watch } from 'vue'
import TotalsFooter from '../Shared/TotalsFooter.vue'

const props = defineProps({
  form: { type: Object, required: true },
  clientesOptions: { type: Array, default: () => [] },
  itemsOptions: { type: Array, default: () => [] },
  unidadesOptions: { type: Array, default: () => [] },
  impostosOptions: { type: Array, default: () => [] },
  submitLabel: { type: String, default: 'Save' },
})
const emit = defineEmits(['submit'])

const selectedCliente = ref(
  props.clientesOptions.find(c => c.id_cliente === props.form.cliente_id) ?? null
)
watch(selectedCliente, (val) => { props.form.cliente_id = val?.id_cliente ?? '' })

function clienteLabel(c) { return c.nome_fantasia || c.razao_social || c.nome || '' }
function itemLabel(i) { return `${i.sku ? i.sku + ' - ' : ''}${i.nome}` }

function addItem() {
  props.form.items.push({ _item_obj: null, item_id: '', descricao_snapshot: '', unidade_medida_id: '', _unidade_obj: null, _imposto_obj: null, imposto_id: '', quantidade: 1, preco_unit: 0, desconto_percent: 0, desconto_valor: 0, aliquota_snapshot: 0, total_linha: 0 })
}
function removeItem(index) { props.form.items.splice(index, 1) }

function onItemSelected(val, index) {
  const row = props.form.items[index]
  if (val) { row.item_id = val.id; row.descricao_snapshot = val.nome; row.preco_unit = val.preco_venda ?? 0 } else { row.item_id = '' }
  recalcLine(index)
}
function onUnidadeSelected(val, index) { props.form.items[index].unidade_medida_id = val?.id ?? '' }
function onImpostoSelected(val, index) { const row = props.form.items[index]; row.imposto_id = val?.id ?? ''; row.aliquota_snapshot = val?.aliquota ?? 0; recalcLine(index) }

function recalcLine(index) {
  const row = props.form.items[index]
  const base = Number(row.preco_unit) * Number(row.quantidade)
  const desc = Number(row.desconto_valor)
  const aliquota = Number(row.aliquota_snapshot)
  const sub = base - desc
  row.total_linha = Math.round((sub + sub * aliquota / 100) * 100) / 100
}
function onDescontoPercent(index) {
  const row = props.form.items[index]
  row.desconto_valor = Math.round(Number(row.preco_unit) * Number(row.quantidade) * (Number(row.desconto_percent) / 100) * 100) / 100
  recalcLine(index)
}

const subtotal = computed(() => props.form.items.reduce((s, i) => s + Number(i.preco_unit) * Number(i.quantidade), 0))
const descontoTotal = computed(() => props.form.items.reduce((s, i) => s + Number(i.desconto_valor), 0))
const totalImpostos = computed(() => props.form.items.reduce((s, i) => { const base = Number(i.preco_unit) * Number(i.quantidade) - Number(i.desconto_valor); return s + base * Number(i.aliquota_snapshot) / 100 }, 0))
const total = computed(() => subtotal.value - descontoTotal.value + totalImpostos.value)
watch(total, (val) => { props.form.total = Math.round(val * 100) / 100 })
</script>

<template>
  <form @submit.prevent="emit('submit')" class="bg-white p-4 rounded shadow dark:bg-slate-900 dark:border dark:border-slate-700 space-y-4 text-slate-700 dark:text-slate-100">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Customer') }} *</label>
        <Multiselect v-model="selectedCliente" :options="clientesOptions" :custom-label="clienteLabel" track-by="id_cliente" :placeholder="$t('Select customer')" select-label="" deselect-label="" class="mt-1" />
        <div v-if="form.errors.cliente_id" class="text-red-600 text-sm mt-1">{{ form.errors.cliente_id }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Order Date') }}</label>
        <input v-model="form.data_pedido" type="date" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium">{{ $t('Notes') }}</label>
        <textarea v-model="form.observacoes" rows="2" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100"></textarea>
      </div>
    </div>

    <div class="flex items-center justify-between">
      <h3 class="font-semibold">{{ $t('Order Items') }}</h3>
      <button type="button" @click="addItem" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:text-slate-100 text-sm">{{ $t('Add Item') }}</button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm border dark:border-slate-700 text-slate-700 dark:text-slate-200">
        <thead class="bg-slate-50 dark:bg-slate-800/70">
          <tr>
            <th class="px-3 py-2 text-left min-w-[220px]">{{ $t('Item') }}</th>
            <th class="px-3 py-2 text-left min-w-[140px]">{{ $t('Description') }}</th>
            <th class="px-3 py-2 text-left w-[80px]">{{ $t('Unit') }}</th>
            <th class="px-3 py-2 text-left w-[80px]">{{ $t('Qty') }}</th>
            <th class="px-3 py-2 text-left w-[100px]">{{ $t('Unit Price') }}</th>
            <th class="px-3 py-2 text-left w-[80px]">{{ $t('Disc %') }}</th>
            <th class="px-3 py-2 text-left w-[90px]">{{ $t('Disc Amount') }}</th>
            <th class="px-3 py-2 text-left min-w-[140px]">{{ $t('Tax') }}</th>
            <th class="px-3 py-2 text-left w-[100px]">{{ $t('Total') }}</th>
            <th class="px-3 py-2 w-[40px]"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in form.items" :key="index" class="border-t dark:border-slate-700">
            <td class="px-3 py-2">
              <Multiselect v-model="item._item_obj" :options="itemsOptions" :custom-label="itemLabel" track-by="id" :placeholder="$t('Search item')" select-label="" deselect-label="" :use-teleport="true" class="w-full text-sm" @update:modelValue="(val) => onItemSelected(val, index)" />
            </td>
            <td class="px-3 py-2"><input v-model="item.descricao_snapshot" class="border rounded px-2 py-1 w-full text-xs dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100"></td>
            <td class="px-3 py-2">
              <Multiselect v-model="item._unidade_obj" :options="unidadesOptions" label="codigo" track-by="id" :placeholder="$t('Unit')" select-label="" deselect-label="" :use-teleport="true" class="text-sm" @update:modelValue="(val) => onUnidadeSelected(val, index)" />
            </td>
            <td class="px-3 py-2"><input v-model="item.quantidade" type="number" step="0.001" min="0.001" class="border rounded px-2 py-1 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" @input="recalcLine(index)"></td>
            <td class="px-3 py-2"><input v-model="item.preco_unit" type="number" step="0.01" min="0" class="border rounded px-2 py-1 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" @input="recalcLine(index)"></td>
            <td class="px-3 py-2"><input v-model="item.desconto_percent" type="number" step="0.01" min="0" max="100" class="border rounded px-2 py-1 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" @input="onDescontoPercent(index)"></td>
            <td class="px-3 py-2"><input v-model="item.desconto_valor" type="number" step="0.01" min="0" class="border rounded px-2 py-1 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" @input="recalcLine(index)"></td>
            <td class="px-3 py-2">
              <Multiselect v-model="item._imposto_obj" :options="impostosOptions" label="nome" track-by="id" :placeholder="$t('Tax')" select-label="" deselect-label="" :use-teleport="true" class="text-sm" @update:modelValue="(val) => onImpostoSelected(val, index)" />
            </td>
            <td class="px-3 py-2 text-right font-medium">{{ Number(item.total_linha).toFixed(2) }}</td>
            <td class="px-3 py-2"><button type="button" @click="removeItem(index)" class="px-2 py-1 rounded bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/40 dark:text-red-300 text-xs">×</button></td>
          </tr>
          <tr v-if="!form.items.length"><td colspan="10" class="px-3 py-3 text-center text-slate-500 dark:text-slate-400">{{ $t('No items added.') }}</td></tr>
        </tbody>
      </table>
    </div>

    <div class="flex justify-end">
      <div class="min-w-[260px]">
        <TotalsFooter
          :subtotal="subtotal"
          :desconto-total="descontoTotal"
          :total-impostos="totalImpostos"
          :total="total"
        />
      </div>
    </div>

    <div class="flex justify-end">
      <button :disabled="form.processing" type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition-colors">{{ submitLabel }}</button>
    </div>
  </form>
</template>
