<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import { useQueryFilters } from '@/composables/useQueryFilters'
import Pagination from '../Shared/Pagination.vue'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  payables: { type: Object, default: () => ({ data: [], links: [] }) },
})

const form = reactive({
  q: props.filters.q ?? '',
  status: props.filters.status ?? '',
  supplier_id: props.filters.supplier_id ?? '',
  data_inicio: props.filters.data_inicio ?? '',
  data_fim: props.filters.data_fim ?? '',
})

const stopSyncFilters = useQueryFilters(form, 'purchases.payables.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Contas a Pagar" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Contas a Pagar</h2>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-5 gap-2">
    <input v-model="form.q" type="text" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500" placeholder="Buscar por numero">
    <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">Status</option>
      <option value="aberto">aberto</option>
      <option value="pago">pago</option>
      <option value="cancelado">cancelado</option>
    </select>
    <input v-model="form.supplier_id" type="number" min="1" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500" placeholder="Fornecedor ID">
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
  </div>

  <div class="overflow-x-auto bg-white rounded shadow">
    <table class="w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-4 py-2 text-left">Documento</th>
          <th class="px-4 py-2 text-left">Status</th>
          <th class="px-4 py-2 text-left">Fornecedor</th>
          <th class="px-4 py-2 text-left">Vencimento</th>
          <th class="px-4 py-2 text-left">Valor</th>
          <th class="px-4 py-2 text-left">Acoes</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="payable in props.payables.data" :key="payable.id" class="border-t">
          <td class="px-4 py-2">{{ payable.numero_documento }}</td>
          <td class="px-4 py-2">{{ payable.status }}</td>
          <td class="px-4 py-2">{{ payable.supplier_id }}</td>
          <td class="px-4 py-2">{{ payable.data_vencimento }}</td>
          <td class="px-4 py-2">{{ payable.valor_total }}</td>
          <td class="px-4 py-2">
            <Link :href="route('purchases.payables.show', payable.id)" class="text-blue-600">Ver</Link>
          </td>
        </tr>
        <tr v-if="!props.payables.data.length">
          <td colspan="6" class="px-4 py-3 text-center text-slate-500">Nenhuma conta encontrada.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    <Pagination :links="props.payables.links" />
  </div>
</template>
