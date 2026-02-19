<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import DataTable, { esc } from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: Object,
})

const form = reactive({
  q: props.filters?.q ?? '',
  status: props.filters?.status ?? '',
})

const dtColumns = [
  {
    data: 'c1',
    title: 'Usuario',
    render: (data, type, row) => {
      if (type !== 'display') {
        return data
      }

      const avatar = esc(row?.avatar || '/img/default-avatar.png')
      const nome = esc(data || '-')
      const email = esc(row?.c2 || '')

      return `
        <div class="flex items-center gap-3">
          <img src="${avatar}" alt="${nome}" class="w-9 h-9 rounded-full object-cover" />
          <div>
            <p class="text-gray-800 font-semibold">${nome}</p>
            <p class="text-gray-500 text-xs">${email}</p>
          </div>
        </div>
      `
    },
  },
  { data: 'roles', title: 'Perfis' },
  {
    data: 'st',
    title: 'Status',
    render: (data) => data
      ? '<span class="text-green-700">Online</span>'
      : '<span class="text-gray-500">Offline</span>',
  },
  { data: 'created_at_fmt', title: 'Ativo desde' },
  { data: 'acoes', title: 'Acoes', orderable: false, searchable: false },
]

const stopSyncFilters = useQueryFilters(form, 'usuario.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Usuarios" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Usuarios</h2>
    <div class="flex gap-4">
      <Link :href="route('categoria.inicio')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-angle-left mr-2"></i>Voltar
      </Link>
      <Link :href="route('usuario.cadastro')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Cadastrar
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
      placeholder="Buscar por nome ou email"
    >
    <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">Status</option>
      <option :value="1">Online</option>
      <option :value="0">Offline</option>
    </select>
  </div>

  <DataTable
    table-id="dt-usuarios"
    :ajax-url="route('usuario.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'asc']]"
    :page-length="10"
    :actions-col-index="4"
  />
</template>

