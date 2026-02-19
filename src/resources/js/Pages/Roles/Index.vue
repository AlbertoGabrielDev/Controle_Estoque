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
})

const dtColumns = [
  {
    data: 'c1',
    title: 'Perfil',
    render: (data, type, row) => {
      if (type !== 'display') {
        return data
      }

      const label = esc(data || '-')
      const url = route('roles.editar', row?.id)
      return `<a href="${url}" class="text-blue-600 hover:underline">${label}</a>`
    },
  },
  { data: 'acoes', title: 'Acoes', orderable: false, searchable: false },
]

const stopSyncFilters = useQueryFilters(form, 'roles.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head title="Roles" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Roles</h2>
    <div class="flex gap-4">
      <Link :href="route('categoria.inicio')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-angle-left mr-2"></i>Voltar
      </Link>
      <Link :href="route('roles.cadastro')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Cadastrar
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500 w-full md:max-w-md"
      placeholder="Buscar por nome do perfil"
    >
  </div>

  <DataTable
    table-id="dt-roles"
    :ajax-url="route('roles.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'asc']]"
    :page-length="10"
    :actions-col-index="1"
  />
</template>

