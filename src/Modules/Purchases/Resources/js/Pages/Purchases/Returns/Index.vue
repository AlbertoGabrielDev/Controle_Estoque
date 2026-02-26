<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
})

const form = reactive({
  q: props.filters.q ?? '',
  status: props.filters.status ?? '',
  data_inicio: props.filters.data_inicio ?? '',
  data_fim: props.filters.data_fim ?? '',
})

const dtColumns = [
  { data: 'c1', title: 'Numero' },
  { data: 'c2', title: 'Status' },
  { data: 'c3', title: 'Pedido' },
  { data: 'c4', title: 'Recebimento' },
  { data: 'c5', title: 'Data' },
  { data: 'acoes', title: 'Acoes', orderable: false, searchable: false },
]

const stopSyncFilters = useQueryFilters(form, 'purchases.returns.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Devolucoes" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Devolucoes</h2>
    <Link :href="route('purchases.returns.create')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
      Nova Devolucao
    </Link>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-4 gap-2">
    <input v-model="form.q" type="text" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500" placeholder="Buscar por numero">
    <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">Status</option>
      <option value="aberta">aberta</option>
      <option value="confirmada">confirmada</option>
      <option value="cancelada">cancelada</option>
    </select>
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
  </div>

  <DataTable
    table-id="dt-purchases-returns"
    :ajax-url="route('purchases.returns.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'desc']]"
    :page-length="10"
    :actions-col-index="5"
  />
</template>
