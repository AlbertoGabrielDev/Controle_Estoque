<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive } from 'vue'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: Object
})

const form = reactive({
  q: props.filters?.q ?? '',
})

const stopSyncFilters = useQueryFilters(form, 'segmentos.index')
onBeforeUnmount(() => stopSyncFilters())

const dtColumns = [
  { data: 'c1',  title: 'Nome', className: 'hidden md:table-cell' },
  { data: 'acoes', title: 'Ações', orderable: false, searchable: false }
]
</script>

<template>
  <Head title="Segmentos" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Segmentos</h1>
    <Link :href="route('segmentos.create')" class="px-3 py-2 rounded bg-blue-600 text-white">Novo Segmento</Link>
  </div>

  <div class="mb-4">
    <input
      v-model="form.q"
      type="text"
      placeholder="Filtrar por nome"
      class="w-full max-w-md px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
    />
  </div>

  <div class="bg-white rounded shadow overflow-x-auto">
    <DataTable 
      table-id="dt-segments" 
      :enhance-only="false" 
      :ajax-url="route('segmentos.data')"
      :ajax-params="form"
      :order="[[0, 'asc']]" 
      :page-length="10" 
      :columns="dtColumns" 
      :actions-col-index="1" 
      :paging="true" />
  </div>

</template>
