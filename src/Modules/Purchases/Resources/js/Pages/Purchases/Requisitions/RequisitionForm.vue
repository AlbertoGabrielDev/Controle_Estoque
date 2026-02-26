<script setup>
import { computed } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Salvar' },
})

/**
 * Add a new item row to the requisition form.
 *
 * @returns {void}
 */
function addItem() {
  props.form.items.push({
    item_id: '',
    descricao_snapshot: '',
    unidade_medida_id: '',
    quantidade: 1,
    preco_estimado: 0,
    imposto_id: '',
    observacoes: '',
  })
}

/**
 * Remove an item row from the requisition form.
 *
 * @param {number} index
 * @returns {void}
 */
function removeItem(index) {
  props.form.items.splice(index, 1)
}

const totalQuantidade = computed(() => props.form.items.reduce((total, item) => {
  const value = Number(item.quantidade)
  return total + (Number.isNaN(value) ? 0 : value)
}, 0))

const totalEstimado = computed(() => props.form.items.reduce((total, item) => {
  const quantidade = Number(item.quantidade)
  const preco = Number(item.preco_estimado)
  const qty = Number.isNaN(quantidade) ? 0 : quantidade
  const unit = Number.isNaN(preco) ? 0 : preco
  return total + (qty * unit)
}, 0))
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Data da Requisicao</label>
        <input v-model="props.form.data_requisicao" type="date" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.data_requisicao" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.data_requisicao }}
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium">Observacoes</label>
        <textarea v-model="props.form.observacoes" rows="2" class="mt-1 border rounded px-3 py-2 w-full"></textarea>
        <div v-if="props.form.errors.observacoes" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.observacoes }}
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between">
      <h3 class="font-semibold">Itens da Requisicao</h3>
      <button type="button" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200" @click="addItem">Adicionar Item</button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm border">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-3 py-2 text-left">Item ID</th>
            <th class="px-3 py-2 text-left">Descricao</th>
            <th class="px-3 py-2 text-left">Unid. Medida ID</th>
            <th class="px-3 py-2 text-left">Quantidade</th>
            <th class="px-3 py-2 text-left">Preco Estimado</th>
            <th class="px-3 py-2 text-left">Imposto ID</th>
            <th class="px-3 py-2 text-left">Observacoes</th>
            <th class="px-3 py-2 text-left">Acoes</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in props.form.items" :key="index" class="border-t">
            <td class="px-3 py-2">
              <input v-model="item.item_id" type="number" min="1" class="border rounded px-2 py-1 w-full">
              <div v-if="props.form.errors[`items.${index}.item_id`]" class="text-red-600 text-xs mt-1">
                {{ props.form.errors[`items.${index}.item_id`] }}
              </div>
            </td>
            <td class="px-3 py-2">
              <input v-model="item.descricao_snapshot" class="border rounded px-2 py-1 w-full">
              <div v-if="props.form.errors[`items.${index}.descricao_snapshot`]" class="text-red-600 text-xs mt-1">
                {{ props.form.errors[`items.${index}.descricao_snapshot`] }}
              </div>
            </td>
            <td class="px-3 py-2">
              <input v-model="item.unidade_medida_id" type="number" min="1" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2">
              <input v-model="item.quantidade" type="number" step="0.001" min="0" class="border rounded px-2 py-1 w-full">
              <div v-if="props.form.errors[`items.${index}.quantidade`]" class="text-red-600 text-xs mt-1">
                {{ props.form.errors[`items.${index}.quantidade`] }}
              </div>
            </td>
            <td class="px-3 py-2">
              <input v-model="item.preco_estimado" type="number" step="0.01" min="0" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2">
              <input v-model="item.imposto_id" type="number" min="1" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2">
              <input v-model="item.observacoes" class="border rounded px-2 py-1 w-full">
            </td>
            <td class="px-3 py-2">
              <button type="button" class="px-2 py-1 rounded bg-red-50 text-red-600" @click="removeItem(index)">Remover</button>
            </td>
          </tr>
          <tr v-if="!props.form.items.length">
            <td colspan="8" class="px-3 py-3 text-center text-slate-500">Nenhum item adicionado.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600">
      <div>Total de itens: {{ props.form.items.length }}</div>
      <div>Quantidade total: {{ totalQuantidade.toFixed(3) }}</div>
      <div>Valor estimado: {{ totalEstimado.toFixed(2) }}</div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">
        {{ submitLabel }}
      </button>
    </div>
  </form>
</template>
