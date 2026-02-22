<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import DataTable, { linkify } from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: Object,
  segmentos: Array,
  ufs: Array
})

const form = reactive({
  q: props.filters.q ?? '',
  uf: props.filters.uf ?? '',
  segment_id: props.filters.segment_id ?? '',
  ativo: props.filters.ativo ?? ''
})
const dtColumns = [
  { data: 'c1', title: 'Código' },
  linkify({ data: 'c2', title: 'Nome' }, { routeName: 'clientes.show', idField: 'id' }),
  { data: 'c3',  title: 'NIF/CIF', className: 'hidden md:table-cell' },
  { data: 'c4',  title: 'WhatsApp',  className: 'hidden lg:table-cell' },
  { data: 'c5',  title: 'UF' },
  { data: 'seg', title: 'Segmento',  className: 'hidden xl:table-cell', orderable: false },
  {
    data: 'st',
    title: 'Ativo',
    render: (data) => data
      ? '<span class="text-green-700">Ativo</span>'
      : '<span class="text-gray-500">Inativo</span>'
  },
  { data: 'acoes', title: 'Ações', orderable: false, searchable: false }
]

const stopSyncFilters = useQueryFilters(form, 'clientes.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>

  <Head title="Clientes" />
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Clientes</h2>
    <div class="flex gap-4">
      <Link :href="route('dashboard.index')"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
      <i class="fas fa-angle-left mr-2"></i>Voltar
      </Link>
      <Link :href="route('clientes.create')"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
      <i class="fas fa-plus mr-2"></i>Novo Cliente
      </Link>
    </div>
  </div>

  <div class="mb-6">
    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
      <input
        v-model="form.q"
        type="text"
        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
        placeholder="Buscar por código, nome ou documento"
      >
      <select v-model="form.uf" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
        <option value="">UF</option>
        <option v-for="u in ufs" :key="u" :value="u">{{ u }}</option>
      </select>
      <select v-model="form.segment_id"
        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
        <option value="">Segmento</option>
        <option v-for="s in segmentos" :key="s.id" :value="s.id">{{ s.nome }}</option>
      </select>
      <select v-model="form.ativo"
        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
        <option value="">Ativo</option>
        <option :value="1">Ativo</option>
        <option :value="0">Inativo</option>
      </select>
    </div>
  </div>

<DataTable
  table-id="dt-clientes"
  :enhance-only="false"
  :ajax-url="route('clientes.data')"
  :ajax-params="form"
  :order="[[0,'asc']]"
  :page-length="10"
  :columns="dtColumns"
  :actions-col-index="7"
/>

</template>
