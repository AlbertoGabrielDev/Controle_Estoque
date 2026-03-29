<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

import { Head, Link } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, reactive } from 'vue'
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
const dtColumns = computed(() => [
  { data: 'c1', title: t('Code') },
  linkify({ data: 'c2', title: t('Name') }, { routeName: 'clientes.show', idField: 'id' }),
  { data: 'c3',  title: 'NIF/CIF', className: 'hidden md:table-cell' },
  { data: 'c4',  title: t('WhatsApp'),  className: 'hidden lg:table-cell' },
  { data: 'c5',  title: t('State (UF)') },
  { data: 'seg', title: t('Segment'),  className: 'hidden xl:table-cell', orderable: false },
  {
    data: 'st',
    title: t('Active'),
    render: (data) => data
      ? `<span class="text-green-700">${t('Active')}</span>`
      : `<span class="text-gray-500">${t('Inactive')}</span>`
  },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false }
])

const stopSyncFilters = useQueryFilters(form, 'clientes.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>

  <Head :title="$t('Customers')" />
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">{{ $t('Customers') }}</h2>
    <div class="flex gap-4">
      <Link :href="route('dashboard.index')"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
      <i class="fas fa-angle-left mr-2"></i>{{ $t('Back') }}
      </Link>
      <Link :href="route('clientes.create')"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
      <i class="fas fa-plus mr-2"></i>{{ $t('New Customer') }}
      </Link>
    </div>
  </div>

  <div class="mb-6">
    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
      <input
        v-model="form.q"
        type="text"
        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
        :placeholder="$t('Search by code, name or doc')"
      >
      <select v-model="form.uf" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
        <option value="">{{ $t('State (UF)') }}</option>
        <option v-for="u in ufs" :key="u" :value="u">{{ u }}</option>
      </select>
      <select v-model="form.segment_id"
        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
        <option value="">{{ $t('Segment') }}</option>
        <option v-for="s in segmentos" :key="s.id" :value="s.id">{{ s.nome }}</option>
      </select>
      <select v-model="form.ativo"
        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
        <option value="">{{ $t('Status') }}</option>
        <option :value="1">{{ $t('Active') }}</option>
        <option :value="0">{{ $t('Inactive') }}</option>
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
