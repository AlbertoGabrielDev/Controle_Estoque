<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive, ref } from 'vue'
import DataTable, { linkify } from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: Object,
})

const filterOpen = ref(false)

const form = reactive({
  q: props.filters?.q ?? '',
  status: props.filters?.status ?? '',
  cod_produto: props.filters?.cod_produto ?? '',
  nome_produto: props.filters?.nome_produto ?? '',
  nome_fornecedor: props.filters?.nome_fornecedor ?? '',
  nome_marca: props.filters?.nome_marca ?? '',
  lote: props.filters?.lote ?? '',
  localizacao: props.filters?.localizacao ?? '',
  quantidade: props.filters?.quantidade ?? '',
  preco_custo: props.filters?.preco_custo ?? '',
  preco_venda: props.filters?.preco_venda ?? '',
  validade: props.filters?.validade ?? '',
  data_chegada: props.filters?.data_chegada ?? '',
  nome_categoria: props.filters?.nome_categoria ?? '',
})

function money(data) {
  return Number(data || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function formatDate(data) {
  if (!data) return '—'
  const dt = new Date(data)
  if (Number.isNaN(dt.getTime())) return data
  return dt.toLocaleDateString('pt-BR')
}

const dtColumns = [
  { data: 'c1', title: 'Código' },
  linkify({ data: 'c2', title: 'Produto' }, { routeName: 'estoque.editar', idField: 'id' }),
  { data: 'c10', title: 'Marca', className: 'hidden lg:table-cell' },
  { data: 'c4', title: 'Custo', render: (data) => money(data), className: 'hidden md:table-cell' },
  { data: 'c5', title: 'Venda', render: (data) => money(data), className: 'hidden md:table-cell' },
  { data: 'c6', title: 'Qtde' },
  { data: 'c7', title: 'Lote' },
  { data: 'c8', title: 'Local', className: 'hidden md:table-cell' },
  { data: 'c9', title: 'Validade', render: (data) => formatDate(data) },
  {
    data: 'st',
    title: 'Status',
    render: (data) => data
      ? '<span class="text-green-700">Ativo</span>'
      : '<span class="text-gray-500">Inativo</span>',
  },
  { data: 'acoes', title: 'Ações', orderable: false, searchable: false },
]

const stopSyncFilters = useQueryFilters(form, 'estoque.index', { debounce: 250 })
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Estoque" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Estoque</h2>
    <div class="flex gap-4">
      <Link :href="route('estoque.historico')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-clock-rotate-left mr-2"></i>Histórico
      </Link>
      <Link :href="route('estoque.cadastro')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Cadastrar Estoque
      </Link>
    </div>
  </div>

  <div class="mb-4">
    <button
      type="button"
      class="w-full md:w-auto flex items-center justify-between px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md"
      @click="filterOpen = !filterOpen"
    >
      <span class="mr-2">Filtrar</span>
      <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': filterOpen }"></i>
    </button>

    <div v-show="filterOpen" class="mt-4 p-4 bg-gray-50 rounded-lg">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <input v-model="form.cod_produto" type="text" placeholder="Código" class="px-3 py-2 border rounded-md">
        <input v-model="form.nome_produto" type="text" placeholder="Produto" class="px-3 py-2 border rounded-md">
        <input v-model="form.nome_fornecedor" type="text" placeholder="Fornecedor" class="px-3 py-2 border rounded-md">
        <input v-model="form.nome_marca" type="text" placeholder="Marca" class="px-3 py-2 border rounded-md">
        <input v-model="form.nome_categoria" type="text" placeholder="Categoria" class="px-3 py-2 border rounded-md">
        <input v-model="form.lote" type="text" placeholder="Lote" class="px-3 py-2 border rounded-md">
        <input v-model="form.localizacao" type="text" placeholder="Localização" class="px-3 py-2 border rounded-md">
        <input v-model="form.quantidade" type="number" placeholder="Quantidade" class="px-3 py-2 border rounded-md">
        <input v-model="form.preco_custo" type="number" step="0.01" placeholder="Preço Custo" class="px-3 py-2 border rounded-md">
        <input v-model="form.preco_venda" type="number" step="0.01" placeholder="Preço Venda" class="px-3 py-2 border rounded-md">
        <input v-model="form.validade" type="date" class="px-3 py-2 border rounded-md">
        <input v-model="form.data_chegada" type="date" class="px-3 py-2 border rounded-md">
        <select v-model="form.status" class="px-3 py-2 border rounded-md">
          <option value="">Status</option>
          <option :value="1">Ativo</option>
          <option :value="0">Inativo</option>
        </select>
        <input
          v-model="form.q"
          type="text"
          placeholder="Busca global"
          class="px-3 py-2 border rounded-md lg:col-span-2"
        >
      </div>
    </div>
  </div>

  <DataTable
    table-id="dt-estoque"
    :ajax-url="route('estoque.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'desc']]"
    :page-length="10"
    :actions-col-index="10"
  />
</template>
