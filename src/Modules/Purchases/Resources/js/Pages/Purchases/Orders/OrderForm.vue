<script setup>
import { ref, watch, onMounted } from 'vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Criar Pedido' },
  requisitionsOptions: { type: Array, default: () => [] },
  suppliersOptions: { type: Array, default: () => [] },
  requisition: { type: Object, default: null },
})

const selectedRequisition = ref(null)
const selectedSupplier = ref(null)

onMounted(() => {
  if (props.requisition) {
    selectedRequisition.value = props.requisitionsOptions.find(r => r.id === props.requisition.id) || null
    props.form.requisition_id = props.requisition.id
  } else if (props.form.requisition_id && props.requisitionsOptions) {
    selectedRequisition.value = props.requisitionsOptions.find(r => r.id === props.form.requisition_id) || null
  }
  if (props.form.supplier_id && props.suppliersOptions) {
    selectedSupplier.value = props.suppliersOptions.find(s => s.id === props.form.supplier_id) || null
  }
})

watch(selectedRequisition, (newVal) => {
  props.form.requisition_id = newVal ? newVal.id : ''
})

watch(selectedSupplier, (newVal) => {
  props.form.supplier_id = newVal ? newVal.id : ''
})

function customRequisitionLabel(option) {
  return option ? option.numero : ''
}

function customSupplierLabel(option) {
  const name = option ? (option.razao_social || option.nome_fornecedor) : ''
  return option ? `${name} (CNPJ: ${option.cnpj})` : ''
}
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow space-y-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium dark:text-slate-200">{{ $t('Requisição') }}</label>
        <Multiselect
          v-model="selectedRequisition"
          :options="props.requisitionsOptions"
          :custom-label="customRequisitionLabel"
          track-by="id"
          :placeholder="$t('Buscar Requisição')"
          select-label="Enter para selecionar"
          deselect-label=""
          :use-teleport="true"
          class="w-full text-sm mt-1"
        />
        <div v-if="props.form.errors.requisition_id" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.requisition_id }}
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium dark:text-slate-200">{{ $t('Fornecedor') }}</label>
        <Multiselect
          v-model="selectedSupplier"
          :options="props.suppliersOptions"
          :custom-label="customSupplierLabel"
          track-by="id"
          :placeholder="$t('Buscar Fornecedor')"
          select-label="Enter para selecionar"
          deselect-label=""
          :use-teleport="true"
          class="w-full text-sm mt-1"
        />
        <div v-if="props.form.errors.supplier_id" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.supplier_id }}
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium dark:text-slate-200">{{ $t('Data Prevista de Entrega') }}</label>
        <input v-model="props.form.data_prevista" type="date" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
        <div v-if="props.form.errors.data_prevista" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.data_prevista }}
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium dark:text-slate-200">{{ $t('Observações') }}</label>
        <input v-model="props.form.observacoes" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
        <div v-if="props.form.errors.observacoes" class="text-red-600 text-sm mt-1">
          {{ props.form.errors.observacoes }}
        </div>
      </div>
    </div>

    <!-- Preview dos itens da Requisição -->
    <div v-if="props.requisition && props.requisition.items && props.requisition.items.length" class="space-y-2">
      <h3 class="font-semibold dark:text-slate-200">Itens da Requisição (serão importados para o Pedido)</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm border purchases-table dark:border-slate-700">
          <thead class="bg-slate-50 dark:bg-slate-800/70">
            <tr>
              <th class="px-3 py-2 text-left dark:text-slate-300">Item ID</th>
              <th class="px-3 py-2 text-left dark:text-slate-300">Descrição</th>
              <th class="px-3 py-2 text-left dark:text-slate-300">Qtd</th>
              <th class="px-3 py-2 text-left dark:text-slate-300">Preço Estimado</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in props.requisition.items" :key="item.id" class="border-t dark:border-slate-700">
              <td class="px-3 py-2 dark:text-slate-300">{{ item.item_id }}</td>
              <td class="px-3 py-2 dark:text-slate-300">{{ item.descricao_snapshot }}</td>
              <td class="px-3 py-2 dark:text-slate-300">{{ item.quantidade }}</td>
              <td class="px-3 py-2 dark:text-slate-300">{{ item.preco_estimado ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition-colors disabled:opacity-50">
        {{ submitLabel }}
      </button>
    </div>
  </form>
</template>
