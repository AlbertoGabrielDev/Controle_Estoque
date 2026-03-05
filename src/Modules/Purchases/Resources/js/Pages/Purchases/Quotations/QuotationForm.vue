<script setup>
import { ref, watch, onMounted } from 'vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Salvar' },
  showSuppliers: { type: Boolean, default: true },
  readonlyRequisition: { type: Boolean, default: false },
  readonlyRequisition: { type: Boolean, default: false },
  requisitionsOptions: { type: Array, default: () => [] },
  suppliersOptions: { type: Array, default: () => [] },
})

const selectedRequisition = ref(null)
const selectedSuppliers = ref([])

onMounted(() => {
  if (props.form.requisition_id && props.requisitionsOptions) {
    selectedRequisition.value = props.requisitionsOptions.find(r => r.id === props.form.requisition_id) || null
  }
  
  if (props.form.supplier_ids && props.form.supplier_ids.length > 0 && props.suppliersOptions) {
    selectedSuppliers.value = props.suppliersOptions.filter(s => props.form.supplier_ids.includes(s.id))
  }
})

watch(selectedRequisition, (newVal) => {
  props.form.requisition_id = newVal ? newVal.id : ''
})

watch(selectedSuppliers, (newValues) => {
  props.form.supplier_ids = newValues ? newValues.map(s => s.id) : []
}, { deep: true })

function customRequisitionLabel(option) {
  return option ? option.numero : ''
}

function customSupplierLabel(option) {
  const name = option ? (option.razao_social || option.nome_fornecedor) : ''
  return option ? `${name} (CNPJ: ${option.cnpj})` : ''
}
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Requisicao ID</label>
        <Multiselect
          v-model="selectedRequisition"
          :options="props.requisitionsOptions"
          :custom-label="customRequisitionLabel"
          track-by="id"
          placeholder="Buscar Requisição"
          select-label="Enter para esc."
          deselect-label=""
          :use-teleport="true"
          :disabled="readonlyRequisition"
          class="w-full text-sm mt-1"
        />
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
      <div>
        <Multiselect
          v-model="selectedSuppliers"
          :options="props.suppliersOptions"
          :custom-label="customSupplierLabel"
          :multiple="true"
          :close-on-select="false"
          :clear-on-select="false"
          track-by="id"
          placeholder="Pesquisar e adicionar Fornecedores"
          select-label="Enter p/ adic."
          deselect-label="Enter p/ remv."
          :use-teleport="true"
          class="w-full text-sm"
        />
        <div v-if="props.form.errors.supplier_ids" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.supplier_ids }}
        </div>
      </div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">
        {{ submitLabel }}
      </button>
    </div>
  </form>
</template>
