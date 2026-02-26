<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import { useQueryFilters } from '@/composables/useQueryFilters'
import Pagination from '../Shared/Pagination.vue'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  orders: { type: Object, default: () => ({ data: [], links: [] }) },
})

const form = reactive({
  q: props.filters.q ?? '',
  status: props.filters.status ?? '',
  supplier_id: props.filters.supplier_id ?? '',
  data_inicio: props.filters.data_inicio ?? '',
  data_fim: props.filters.data_fim ?? '',
})

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

  <div class="overflow-x-auto bg-white rounded shadow">
    <table class="w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-4 py-2 text-left">Numero</th>
          <th class="px-4 py-2 text-left">Status</th>
          <th class="px-4 py-2 text-left">Fornecedor</th>
          <th class="px-4 py-2 text-left">Data</th>
          <th class="px-4 py-2 text-left">Total</th>
          <th class="px-4 py-2 text-left">Acoes</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in props.orders.data" :key="order.id" class="border-t">
          <td class="px-4 py-2">{{ order.numero }}</td>
          <td class="px-4 py-2">{{ order.status }}</td>
          <td class="px-4 py-2">{{ order.supplier_id }}</td>
          <td class="px-4 py-2">{{ order.data_emissao }}</td>
          <td class="px-4 py-2">{{ order.total }}</td>
          <td class="px-4 py-2">
            <Link :href="route('purchases.orders.show', order.id)" class="text-blue-600">Ver</Link>
          </td>
        </tr>
        <tr v-if="!props.orders.data.length">
          <td colspan="6" class="px-4 py-3 text-center text-slate-500">Nenhum pedido encontrado.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    <Pagination :links="props.orders.links" />
  </div>
</template>
