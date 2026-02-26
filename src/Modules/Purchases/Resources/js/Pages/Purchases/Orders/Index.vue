<script setup>
import { Head } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
})

const form = reactive({
  q: props.filters.q ?? '',
  status: props.filters.status ?? '',
  supplier_id: props.filters.supplier_id ?? '',
  data_inicio: props.filters.data_inicio ?? '',
  data_fim: props.filters.data_fim ?? '',
})

const dtColumns = [
  { data: 'c1', title: 'Numero' },
  { data: 'c2', title: 'Status' },
  { data: 'c3', title: 'Fornecedor' },
  { data: 'c4', title: 'Data' },
  { data: 'c5', title: 'Total' },
  { data: 'acoes', title: 'Acoes', orderable: false, searchable: false },
]

const stopSyncFilters = useQueryFilters(form, 'purchases.orders.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Pedidos" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Pedidos</h2>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-5 gap-2">
    <input v-model="form.q" type="text" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500" placeholder="Buscar por numero">
    <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">Status</option>
      <option value="emitido">emitido</option>
      <option value="parcialmente_recebido">parcialmente_recebido</option>
      <option value="recebido">recebido</option>
      <option value="cancelado">cancelado</option>
      <option value="fechado">fechado</option>
    </select>
    <input v-model="form.supplier_id" type="number" min="1" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500" placeholder="Fornecedor ID">
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
  </div>

  <DataTable
    table-id="dt-purchases-orders"
    :ajax-url="route('purchases.orders.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'desc']]"
    :page-length="10"
    :actions-col-index="5"
  />
</template>
