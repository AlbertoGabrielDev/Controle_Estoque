<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: { type: Object, default: () => ({}) },
})
const { t } = useI18n()

const form = reactive({
  q: props.filters.q ?? '',
  status: props.filters.status ?? '',
  data_inicio: props.filters.data_inicio ?? '',
  data_fim: props.filters.data_fim ?? '',
})

const dtColumns = computed(() => [
  { data: 'id', title: '#', width: '60px' },
  { data: 'c1', title: t('Number') },
  { data: 'c2', title: t('Status') },
  { data: 'c3', title: t('Customer') },
  { data: 'c4', title: t('Issue Date') },
  { data: 'c5', title: t('Validity') },
  { data: 'c6', title: t('Total') },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])

const stopSync = useQueryFilters(form, 'commercial.proposals.index')
onBeforeUnmount(() => stopSync())
</script>

<template>
  <Head :title="$t('Commercial Proposals')" />
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('Commercial Proposals') }}</h2>
    <Link :href="route('commercial.proposals.create')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-md text-gray-800 dark:text-slate-100 transition-colors">{{ $t('New Proposal') }}</Link>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-4 gap-2">
    <input v-model="form.q" type="text" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" :placeholder="$t('Search...')">
    <select v-model="form.status" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      <option value="">{{ $t('All Statuses') }}</option>
      <option value="rascunho">{{ $t('Draft') }}</option>
      <option value="enviada">{{ $t('Sent') }}</option>
      <option value="aprovada">{{ $t('Approved') }}</option>
      <option value="rejeitada">{{ $t('Rejected') }}</option>
      <option value="vencida">{{ $t('Expired') }}</option>
      <option value="convertida">{{ $t('Converted') }}</option>
    </select>
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
  </div>

  <DataTable
    table-id="dt-commercial-proposals"
    :ajax-url="route('commercial.proposals.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'desc']]"
    :page-length="15"
    :actions-col-index="7"
  />
</template>
