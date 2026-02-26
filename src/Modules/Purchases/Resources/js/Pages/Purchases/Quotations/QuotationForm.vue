<script setup>
import { ref } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Salvar' },
  showSuppliers: { type: Boolean, default: true },
  readonlyRequisition: { type: Boolean, default: false },
})

const supplierInput = ref('')

/**
 * Add a supplier id to the quotation form list.
 *
 * @returns {void}
 */
function addSupplierId() {
  const value = Number(supplierInput.value)
  if (!value || Number.isNaN(value)) {
    supplierInput.value = ''
    return
  }

  if (!props.form.supplier_ids.includes(value)) {
    props.form.supplier_ids.push(value)
  }

  supplierInput.value = ''
}

/**
 * Remove a supplier id from the quotation form list.
 *
 * @param {number} index
 * @returns {void}
 */
function removeSupplierId(index) {
  props.form.supplier_ids.splice(index, 1)
}
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Requisicao ID</label>
        <input
          v-model="props.form.requisition_id"
          type="number"
          min="1"
          class="mt-1 border rounded px-3 py-2 w-full"
          :readonly="readonlyRequisition"
        >
        <div v-if="props.form.errors.requisition_id" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.requisition_id }}
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium">Data Limite</label>
        <input v-model="props.form.data_limite" type="date" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.data_limite" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.data_limite }}
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium">Observacoes</label>
        <input v-model="props.form.observacoes" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.observacoes" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.observacoes }}
        </div>
      </div>
    </div>

    <div v-if="props.showSuppliers" class="space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">Fornecedores Convidados</h3>
      </div>
      <div class="flex flex-wrap gap-2">
        <input v-model="supplierInput" type="number" min="1" placeholder="Fornecedor ID" class="border rounded px-3 py-2">
        <button type="button" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200" @click="addSupplierId">Adicionar</button>
      </div>
      <div v-if="props.form.supplier_ids.length" class="flex flex-wrap gap-2">
        <span
          v-for="(supplierId, index) in props.form.supplier_ids"
          :key="`${supplierId}-${index}`"
          class="px-2 py-1 rounded bg-slate-100 text-slate-700"
        >
          {{ supplierId }}
          <button type="button" class="ml-2 text-red-600" @click="removeSupplierId(index)">x</button>
        </span>
      </div>
      <div v-else class="text-sm text-slate-500">Nenhum fornecedor adicionado.</div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">
        {{ submitLabel }}
      </button>
    </div>
  </form>
</template>
