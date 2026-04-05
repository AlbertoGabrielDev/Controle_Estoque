<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({ filters: { type: Object, default: () => ({}) } })
const form = reactive({ q: props.filters.q ?? '', status: props.filters.status ?? '', data_inicio: props.filters.data_inicio ?? '', data_fim: props.filters.data_fim ?? '' })
const { t } = useI18n()

const dtColumns = computed(() => [
  { data: 'id', title: '#', width: '60px' },
  { data: 'c1', title: t('Number') }, { data: 'c2', title: t('Status') }, { data: 'c3', title: t('Customer') },
  { data: 'c4', title: t('Issue Date') }, { data: 'c5', title: t('Due Date') }, { data: 'c6', title: t('Total') },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])

const stopSync = useQueryFilters(form, 'commercial.invoices.index')
onBeforeUnmount(() => stopSync())
</script>

<template>
  <Head :title="$t('Invoices')" />
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('Invoices') }}</h2>
  </div>
  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-4 gap-2">
    <input v-model="form.q" type="text" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" :placeholder="$t('Search...')">
    <select v-model="form.status" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      <option value="">{{ $t('All') }}</option>
      <option value="emitida">{{ $t('Issued') }}</option>
      <option value="parcial">{{ $t('Partial') }}</option>
      <option value="paga">{{ $t('Paid') }}</option>
      <option value="cancelada">{{ $t('Canceled') }}</option>
    </select>
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
  </div>
  <DataTable table-id="dt-commercial-invoices" :ajax-url="route('commercial.invoices.data')" :ajax-params="form" :columns="dtColumns" :order="[[0, 'desc']]" :page-length="15" :actions-col-index="7" />
</template>
