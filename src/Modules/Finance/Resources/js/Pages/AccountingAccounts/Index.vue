<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive, computed } from 'vue'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  filters: Object,
})

const form = reactive({
  q: props.filters?.q ?? '',
  ativo: props.filters?.ativo ?? '',
})

const dtColumns = computed(() => [
  { data: 'c1', title: t('Code') },
  { data: 'c2', title: t('Name') },
  { data: 'c3', title: t('Type') },
  { data: 'c4', title: t('Parent Account'), className: 'hidden lg:table-cell' },
  {
    data: 'c5',
    title: t('Accepts Entry'),
    render: (data) => data
      ? `<span class="text-green-700">${t('Yes')}</span>`
      : `<span class="text-gray-500">${t('No')}</span>`,
  },
  {
    data: 'st',
    title: t('Active'),
    render: (data) => data
      ? `<span class="text-green-700">${t('Active')}</span>`
      : `<span class="text-gray-500">${t('Inactive')}</span>`,
  },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])

const stopSyncFilters = useQueryFilters(form, 'contas_contabeis.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head :title="$t('Accounting Accounts')" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">{{ $t('Accounting Accounts') }}</h2>
    <div class="flex gap-4">
      <Link :href="route('contas_contabeis.create')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>{{ $t('New Accounting Account') }}
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
      :placeholder="$t('Search by code, name or type')"
    >
    <select v-model="form.ativo" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('Active') }}</option>
      <option :value="1">{{ $t('Active') }}</option>
      <option :value="0">{{ $t('Inactive') }}</option>
    </select>
  </div>

  <DataTable
    table-id="dt-contas-contabeis"
    :ajax-url="route('contas_contabeis.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'asc']]"
    :page-length="10"
    :actions-col-index="6"
  />
</template>
