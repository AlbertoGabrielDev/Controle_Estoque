<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import { useQueryFilters } from '@/composables/useQueryFilters'
import Pagination from '../Shared/Pagination.vue'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  quotations: { type: Object, default: () => ({ data: [], links: [] }) },
})

const form = reactive({
  q: props.filters.q ?? '',
  status: props.filters.status ?? '',
  supplier_id: props.filters.supplier_id ?? '',
  data_inicio: props.filters.data_inicio ?? '',
  data_fim: props.filters.data_fim ?? '',
})

const stopSyncFilters = useQueryFilters(form, 'purchases.quotations.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Cotacoes" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Cotacoes</h2>
    <div class="flex gap-4">
      <Link :href="route('purchases.quotations.create')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        Nova Cotacao
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-5 gap-2">
    <input v-model="form.q" type="text" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500" placeholder="Buscar por numero">
    <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">Status</option>
      <option value="aberta">aberta</option>
      <option value="encerrada">encerrada</option>
      <option value="cancelada">cancelada</option>
    </select>
    <input v-model="form.supplier_id" type="number" min="1" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500" placeholder="Fornecedor ID">
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
  </div>

  <div class="overflow-x-auto bg-white rounded shadow">
    <table class="w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-4 py-2 text-left">Numero</th>
          <th class="px-4 py-2 text-left">Status</th>
          <th class="px-4 py-2 text-left">Requisicao</th>
          <th class="px-4 py-2 text-left">Data Limite</th>
          <th class="px-4 py-2 text-left">Acoes</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="quotation in props.quotations.data" :key="quotation.id" class="border-t">
          <td class="px-4 py-2">{{ quotation.numero }}</td>
          <td class="px-4 py-2">{{ quotation.status }}</td>
          <td class="px-4 py-2">{{ quotation.requisition_id }}</td>
          <td class="px-4 py-2">{{ quotation.data_limite ?? '-' }}</td>
          <td class="px-4 py-2">
            <div class="flex flex-wrap gap-2">
              <Link :href="route('purchases.quotations.show', quotation.id)" class="text-blue-600">Ver</Link>
              <Link v-if="quotation.status === 'aberta'" :href="route('purchases.quotations.edit', quotation.id)" class="text-blue-600">Editar</Link>
            </div>
          </td>
        </tr>
        <tr v-if="!props.quotations.data.length">
          <td colspan="5" class="px-4 py-3 text-center text-slate-500">Nenhuma cotacao encontrada.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    <Pagination :links="props.quotations.links" />
  </div>
</template>
