<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import DataTable, { linkify } from '../../components/DataTable.vue'

const props = defineProps({
  segmentos: Object,
  q: String
})
const q = ref(props.q ?? '')
watch(q, () => {
  router.get(route('segmentos.index'), { q: q.value }, { preserveState: true, replace: true })
})

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

  <div class="bg-white rounded shadow overflow-x-auto">
    <DataTable 
      table-id="dt-segments" 
      :enhance-only="false" 
      :ajax-url="route('segmentos.data')"
      :order="[[0, 'asc']]" 
      :page-length="10" 
      :columns="dtColumns" 
      :actions-col-index="1" 
      :paging="true" />
  </div>

</template>
