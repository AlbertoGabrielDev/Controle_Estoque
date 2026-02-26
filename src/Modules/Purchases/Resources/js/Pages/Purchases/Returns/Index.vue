<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import { useQueryFilters } from '@/composables/useQueryFilters'
import Pagination from '../Shared/Pagination.vue'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  returns: { type: Object, default: () => ({ data: [], links: [] }) },
})

const form = reactive({
  q: props.filters.q ?? '',
  status: props.filters.status ?? '',
  data_inicio: props.filters.data_inicio ?? '',
  data_fim: props.filters.data_fim ?? '',
})

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

  <div class="overflow-x-auto bg-white rounded shadow">
    <table class="w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-4 py-2 text-left">Numero</th>
          <th class="px-4 py-2 text-left">Status</th>
          <th class="px-4 py-2 text-left">Pedido</th>
          <th class="px-4 py-2 text-left">Recebimento</th>
          <th class="px-4 py-2 text-left">Data</th>
          <th class="px-4 py-2 text-left">Acoes</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="purchaseReturn in props.returns.data" :key="purchaseReturn.id" class="border-t">
          <td class="px-4 py-2">{{ purchaseReturn.numero }}</td>
          <td class="px-4 py-2">{{ purchaseReturn.status }}</td>
          <td class="px-4 py-2">{{ purchaseReturn.order_id ?? '-' }}</td>
          <td class="px-4 py-2">{{ purchaseReturn.receipt_id ?? '-' }}</td>
          <td class="px-4 py-2">{{ purchaseReturn.data_devolucao }}</td>
          <td class="px-4 py-2">
            <Link :href="route('purchases.returns.show', purchaseReturn.id)" class="text-blue-600">Ver</Link>
          </td>
        </tr>
        <tr v-if="!props.returns.data.length">
          <td colspan="6" class="px-4 py-3 text-center text-slate-500">Nenhuma devolucao encontrada.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    <Pagination :links="props.returns.links" />
  </div>
</template>
