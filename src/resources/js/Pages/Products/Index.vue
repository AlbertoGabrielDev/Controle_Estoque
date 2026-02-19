<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import DataTable, { esc, linkify } from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: Object,
})

const form = reactive({
  q: props.filters?.q ?? '',
  status: props.filters?.status ?? '',
})

function nutritionSummary(data) {
  if (data == null || data === '') {
    return '—'
  }

  let value = data
  if (typeof value === 'string') {
    try {
      value = JSON.parse(value)
    } catch {
      return value.length > 60 ? `${value.slice(0, 60)}...` : value
    }
  }

  if (Array.isArray(value)) {
    return value.length > 0 ? `Itens: ${value.length}` : '—'
  }

  if (typeof value === 'object') {
    const keys = Object.keys(value)
    return keys.length > 0 ? keys.join(', ') : '—'
  }

  return String(value)
}

const dtColumns = [
  { data: 'c1', title: 'Código' },
  linkify({ data: 'c2', title: 'Nome' }, { routeName: 'produtos.editar', idField: 'id' }),
  { data: 'c3', title: 'Descrição', className: 'hidden lg:table-cell' },
  { data: 'c4', title: 'Unidade' },
  {
    data: 'c5',
    title: 'Nutrição',
    orderable: false,
    searchable: false,
    render: (data, type) => {
      if (type !== 'display') return data
      return `<span class="text-slate-600">${esc(nutritionSummary(data))}</span>`
    },
  },
  {
    data: 'st',
    title: 'Status',
    render: (data) => data
      ? '<span class="text-green-700">Ativo</span>'
      : '<span class="text-gray-500">Inativo</span>',
  },
  { data: 'acoes', title: 'Ações', orderable: false, searchable: false },
]

const stopSyncFilters = useQueryFilters(form, 'produtos.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Produtos" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Produtos</h2>
    <div class="flex gap-4">
      <Link :href="route('categoria.inicio')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-angle-left mr-2"></i>Voltar
      </Link>
      <Link :href="route('produtos.cadastro')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Cadastrar
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
      placeholder="Buscar por código, nome, descrição ou unidade"
    >
    <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">Status</option>
      <option :value="1">Ativo</option>
      <option :value="0">Inativo</option>
    </select>
  </div>

  <DataTable
    table-id="dt-produtos"
    :ajax-url="route('produtos.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[1, 'asc']]"
    :page-length="15"
    :actions-col-index="6"
  />
</template>
