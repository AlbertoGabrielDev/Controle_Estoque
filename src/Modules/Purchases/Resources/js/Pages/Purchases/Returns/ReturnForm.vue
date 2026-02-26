<script setup>
import { computed } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Salvar' },
})

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
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Receipt ID</label>
        <input v-model="props.form.receipt_id" type="number" min="1" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.receipt_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.receipt_id }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">Order ID</label>
        <input v-model="props.form.order_id" type="number" min="1" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.order_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.order_id }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">Data Devolucao</label>
        <input v-model="props.form.data_devolucao" type="date" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.data_devolucao" class="text-red-600 text-sm mt-1">{{ props.form.errors.data_devolucao }}</div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Motivo</label>
      <input v-model="props.form.motivo" class="mt-1 border rounded px-3 py-2 w-full">
      <div v-if="props.form.errors.motivo" class="text-red-600 text-sm mt-1">{{ props.form.errors.motivo }}</div>
    </div>

    <div class="flex items-center justify-between">
      <h3 class="font-semibold">Itens da Devolucao</h3>
      <button type="button" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200" @click="addItem">Adicionar Item</button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm border">
        <thead class="bg-slate-50">
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
          <tr v-for="(item, index) in props.form.items" :key="index" class="border-t">
            <td class="px-3 py-2">
              <input v-model="item.receipt_item_id" type="number" min="1" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2">
              <input v-model="item.order_item_id" type="number" min="1" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2">
              <input v-model="item.item_id" type="number" min="1" class="border rounded px-2 py-1 w-full">
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
              <button type="button" class="px-2 py-1 rounded bg-red-50 text-red-600" @click="removeItem(index)">Remover</button>
            </td>
          </tr>
          <tr v-if="!props.form.items.length">
            <td colspan="6" class="px-3 py-3 text-center text-slate-500">Nenhum item adicionado.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600">
      <div>Total de itens: {{ props.form.items.length }}</div>
      <div>Quantidade total: {{ totalQuantidade.toFixed(3) }}</div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">
        {{ submitLabel }}
      </button>
    </div>
  </form>
</template>
