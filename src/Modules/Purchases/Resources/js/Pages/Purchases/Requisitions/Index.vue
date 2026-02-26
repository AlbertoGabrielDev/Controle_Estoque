<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import { useQueryFilters } from '@/composables/useQueryFilters'
import Pagination from '../Shared/Pagination.vue'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
  requisitions: { type: Object, default: () => ({ data: [], links: [] }) },
})

const form = reactive({
  q: props.filters.q ?? '',
  status: props.filters.status ?? '',
  data_inicio: props.filters.data_inicio ?? '',
  data_fim: props.filters.data_fim ?? '',
})

const stopSyncFilters = useQueryFilters(form, 'purchases.requisitions.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Requisicoes" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Requisicoes</h2>
    <div class="flex gap-4">
      <Link :href="route('purchases.requisitions.create')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        Nova Requisicao
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-4 gap-2">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
      placeholder="Buscar por numero"
    >
    <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">Status</option>
      <option value="draft">draft</option>
      <option value="aprovado">aprovado</option>
      <option value="cancelado">cancelado</option>
      <option value="fechado">fechado</option>
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
          <th class="px-4 py-2 text-left">Data</th>
          <th class="px-4 py-2 text-left">Itens</th>
          <th class="px-4 py-2 text-left">Acoes</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="requisition in props.requisitions.data" :key="requisition.id" class="border-t">
          <td class="px-4 py-2">{{ requisition.numero }}</td>
          <td class="px-4 py-2">{{ requisition.status }}</td>
          <td class="px-4 py-2">{{ requisition.data_requisicao ?? '-' }}</td>
          <td class="px-4 py-2">{{ requisition.items?.length ?? 0 }}</td>
          <td class="px-4 py-2">
            <div class="flex flex-wrap gap-2">
              <Link :href="route('purchases.requisitions.show', requisition.id)" class="text-blue-600">Ver</Link>
              <Link v-if="requisition.status === 'draft'" :href="route('purchases.requisitions.edit', requisition.id)" class="text-blue-600">Editar</Link>
            </div>
          </td>
        </tr>
        <tr v-if="!props.requisitions.data.length">
          <td colspan="5" class="px-4 py-3 text-center text-slate-500">Nenhuma requisicao encontrada.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    <Pagination :links="props.requisitions.links" />
  </div>
</template>
