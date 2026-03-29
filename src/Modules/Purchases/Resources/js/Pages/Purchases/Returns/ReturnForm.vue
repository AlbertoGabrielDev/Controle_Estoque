<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

import { computed } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Salvar' },
  itemsOptions: { type: Array, default: () => [] },
})

import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

/**
 * Add a new item row to the return form.
 *
 * @returns {void}
 */
function addItem() {
  props.form.items.push({
    receipt_item_id: '',
    order_item_id: '',
    item_id: '',
    _item_obj: null,
    quantidade_devolvida: 1,
    observacoes: '',
  })
}

/**
 * Remove an item row from the return form.
 *
 * @param {number} index
 * @returns {void}
 */
function removeItem(index) {
  props.form.items.splice(index, 1)
}

const totalQuantidade = computed(() => props.form.items.reduce((total, item) => {
  const value = Number(item.quantidade_devolvida)
  return total + (Number.isNaN(value) ? 0 : value)
}, 0))

function onItemSelected(selectedItem, index) {
  const row = props.form.items[index]
  if (selectedItem) {
    row.item_id = selectedItem.id
  } else {
    row.item_id = ''
  }
}

function customItemLabel(option) {
  return `${option.sku ? option.sku + ' - ' : ''}${option.nome}`
}
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow dark:bg-slate-900 dark:border dark:border-slate-700 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Receipt ID') }}</label>
        <input v-model="props.form.receipt_id" type="number" min="1" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.receipt_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.receipt_id }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Order ID') }}</label>
        <input v-model="props.form.order_id" type="number" min="1" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.order_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.order_id }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Data Devolucao') }}</label>
        <input v-model="props.form.data_devolucao" type="date" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.data_devolucao" class="text-red-600 text-sm mt-1">{{ props.form.errors.data_devolucao }}</div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">{{ $t('Motivo') }}</label>
      <input v-model="props.form.motivo" class="mt-1 border rounded px-3 py-2 w-full">
      <div v-if="props.form.errors.motivo" class="text-red-600 text-sm mt-1">{{ props.form.errors.motivo }}</div>
    </div>

    <div class="flex items-center justify-between">
      <h3 class="font-semibold">Itens da Devolucao</h3>
      <button type="button" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-100" @click="addItem">{{ $t('Adicionar Item') }}</button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm border purchases-table dark:border-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-800/70">
          <tr>
            <th class="px-3 py-2 text-left">Receipt Item ID</th>
            <th class="px-3 py-2 text-left">Order Item ID</th>
            <th class="px-3 py-2 text-left">Item ID</th>
            <th class="px-3 py-2 text-left">Quantidade</th>
            <th class="px-3 py-2 text-left">Observacoes</th>
            <th class="px-3 py-2 text-left">Acoes</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in props.form.items" :key="index" class="border-t dark:border-slate-700">
            <td class="px-3 py-2">
              <input v-model="item.receipt_item_id" type="number" min="1" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2">
              <input v-model="item.order_item_id" type="number" min="1" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2 min-w-[250px]">
              <Multiselect
                v-model="item._item_obj"
                :options="props.itemsOptions"
                :custom-label="customItemLabel"
                track-by="id"
                :placeholder="$t('Buscar Item')"
                select-label="Enter para esc."
                deselect-label=""
                :use-teleport="true"
                class="w-full text-sm"
                @update:modelValue="(val) => onItemSelected(val, index)"
              />
              <div v-if="props.form.errors[`items.${index}.item_id`]" class="text-red-600 text-xs mt-1">
                {{ props.form.errors[`items.${index}.item_id`] }}
              </div>
            </td>
            <td class="px-3 py-2">
              <input v-model="item.quantidade_devolvida" type="number" step="0.001" min="0" class="border rounded px-2 py-1 w-full">
              <div v-if="props.form.errors[`items.${index}.quantidade_devolvida`]" class="text-red-600 text-xs mt-1">
                {{ props.form.errors[`items.${index}.quantidade_devolvida`] }}
              </div>
            </td>
            <td class="px-3 py-2">
              <input v-model="item.observacoes" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2">
              <button type="button" class="px-2 py-1 rounded bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/40 dark:text-red-300 dark:hover:bg-red-900/60 transition-colors" @click="removeItem(index)">{{ $t('Remove') }}</button>
            </td>
          </tr>
          <tr v-if="!props.form.items.length">
            <td colspan="6" class="px-3 py-3 text-center text-slate-500 dark:text-slate-400">Nenhum item adicionado.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600 dark:text-slate-300">
      <div>Total de itens: {{ props.form.items.length }}</div>
      <div>Quantidade total: {{ totalQuantidade.toFixed(3) }}</div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition-colors">
        {{ submitLabel }}
      </button>
    </div>
  </form>
</template>
