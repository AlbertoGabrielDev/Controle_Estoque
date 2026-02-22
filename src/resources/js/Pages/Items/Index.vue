<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: Object,
})

const form = reactive({
  q: props.filters?.q ?? '',
  tipo: props.filters?.tipo ?? '',
  ativo: props.filters?.ativo ?? '',
})

const dtColumns = [
  { data: 'c1', title: 'SKU' },
  { data: 'c2', title: 'Nome' },
  { data: 'c3', title: 'Tipo' },
  { data: 'c4', title: 'Categoria', className: 'hidden lg:table-cell' },
  { data: 'c5', title: 'Unidade', className: 'hidden lg:table-cell' },
  { data: 'c6', title: 'Preço Base' },
  {
    data: 'st',
    title: 'Ativo',
    render: (data) => data
      ? '<span class="text-green-700">Ativo</span>'
      : '<span class="text-gray-500">Inativo</span>',
  },
  { data: 'acoes', title: 'Ações', orderable: false, searchable: false },
]

const stopSyncFilters = useQueryFilters(form, 'itens.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Itens" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Itens</h2>
    <div class="flex gap-4">
      <Link :href="route('itens.create')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Novo Item
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-3 gap-2">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
      placeholder="Buscar por SKU ou nome"
    >
    <select v-model="form.tipo" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">Tipo</option>
      <option value="produto">Produto</option>
      <option value="servico">Serviço</option>
    </select>
    <select v-model="form.ativo" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">Ativo</option>
      <option :value="1">Ativo</option>
      <option :value="0">Inativo</option>
    </select>
  </div>

  <DataTable
    table-id="dt-itens"
    :ajax-url="route('itens.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'asc']]"
    :page-length="10"
    :actions-col-index="7"
  />
</template>
