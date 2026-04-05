<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({ filters: { type: Object, default: () => ({}) } })
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
  { data: 'c4', title: t('Order Date') },
  { data: 'c5', title: t('Total') },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])

const stopSync = useQueryFilters(form, 'commercial.orders.index')
onBeforeUnmount(() => stopSync())
</script>

<template>
  <Head :title="$t('Sales Orders')" />
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('Sales Orders') }}</h2>
    <Link :href="route('commercial.orders.create')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-md text-gray-800 dark:text-slate-100 transition-colors">{{ $t('New Sales Order') }}</Link>
  </div>
  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-4 gap-2">
    <input v-model="form.q" type="text" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" :placeholder="$t('Search...')">
    <select v-model="form.status" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      <option value="">{{ $t('All') }}</option>
      <option value="rascunho">{{ $t('Draft') }}</option>
      <option value="confirmado">{{ $t('Confirmed') }}</option>
      <option value="faturado_parcial">{{ $t('Partially Invoiced') }}</option>
      <option value="faturado_total">{{ $t('Fully Invoiced') }}</option>
      <option value="cancelado">{{ $t('Canceled') }}</option>
      <option value="fechado">{{ $t('Closed') }}</option>
    </select>
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
  </div>
  <DataTable table-id="dt-commercial-orders" :ajax-url="route('commercial.orders.data')" :ajax-params="form" :columns="dtColumns" :order="[[0, 'desc']]" :page-length="15" :actions-col-index="6" />
</template>
