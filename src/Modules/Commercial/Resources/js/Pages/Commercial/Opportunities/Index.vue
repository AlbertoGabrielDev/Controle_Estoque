<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

import { Head, Link } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, reactive } from 'vue'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
})

const form = reactive({
  q: props.filters.q ?? '',
  status: props.filters.status ?? '',
  data_inicio: props.filters.data_inicio ?? '',
  data_fim: props.filters.data_fim ?? '',
})

const dtColumns = computed(() => [
  { data: 'id', title: '#', width: '60px' },
  { data: 'c1', title: t('Code') },
  { data: 'c2', title: t('Status') },
  { data: 'c3', title: t('Customer') },
  { data: 'c4', title: t('Estimated Value') },
  { data: 'c5', title: t('Expected Close') },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])

const stopSync = useQueryFilters(form, 'commercial.opportunities.index')
onBeforeUnmount(() => stopSync())
</script>

<template>
  <Head :title="$t('Opportunities')" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('Opportunities') }}</h2>
    <Link :href="route('commercial.opportunities.create')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-md text-gray-800 dark:text-slate-100 transition-colors">
      {{ $t('New Opportunity') }}
    </Link>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-4 gap-2">
    <input v-model="form.q" type="text" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500" :placeholder="$t('Search...')">
    <select v-model="form.status" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('All Statuses') }}</option>
      <option value="novo">{{ $t('New') }}</option>
      <option value="em_contato">{{ $t('In Contact') }}</option>
      <option value="proposta_enviada">{{ $t('Proposal Sent') }}</option>
      <option value="negociacao">{{ $t('Negotiation') }}</option>
      <option value="ganho">{{ $t('Won') }}</option>
      <option value="perdido">{{ $t('Lost') }}</option>
    </select>
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500">
  </div>

  <DataTable
    table-id="dt-commercial-opportunities"
    :ajax-url="route('commercial.opportunities.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'desc']]"
    :page-length="15"
    :actions-col-index="6"
  />
</template>
