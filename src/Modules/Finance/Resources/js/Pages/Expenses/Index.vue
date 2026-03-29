<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive, computed } from 'vue'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps({
  filters: Object,
  centrosCusto: { type: Array, default: () => [] },
  contasContabeis: { type: Array, default: () => [] },
  fornecedores: { type: Array, default: () => [] },
})

const form = reactive({
  q: props.filters?.q ?? '',
  ativo: props.filters?.ativo ?? '',
  centro_custo_id: props.filters?.centro_custo_id ?? '',
  conta_contabil_id: props.filters?.conta_contabil_id ?? '',
  fornecedor_id: props.filters?.fornecedor_id ?? '',
  data_inicio: props.filters?.data_inicio ?? '',
  data_fim: props.filters?.data_fim ?? '',
})

const currentLocale = computed(() => {
  if (locale.value === 'en') return 'en-US'
  if (locale.value === 'es') return 'es-ES'
  return 'pt-BR'
})

const currencySymbol = computed(() => {
  if (locale.value === 'en') return 'USD'
  if (locale.value === 'es') return 'EUR'
  return 'BRL'
})

const money = (value) => Number(value || 0).toLocaleString(currentLocale.value, { 
  style: 'currency', 
  currency: currencySymbol.value 
})

const fmtDate = (value) => {
  if (!value) return ''
  const dt = new Date(value)
  return Number.isNaN(dt.getTime()) ? String(value) : dt.toLocaleDateString(currentLocale.value)
}

const dtColumns = computed(() => [
  {
    data: 'c1',
    title: t('Date'),
    render: (data, type) => (type === 'display' ? fmtDate(data) : data),
  },
  { data: 'c2', title: t('Description') },
  {
    data: 'c3',
    title: t('Amount'),
    render: (data, type) => (type === 'display' ? money(data) : data),
  },
  { data: 'c4', title: t('Cost Center'), className: 'hidden lg:table-cell' },
  { data: 'c5', title: t('Accounting Account'), className: 'hidden lg:table-cell' },
  { data: 'c6', title: t('Supplier'), className: 'hidden lg:table-cell' },
  {
    data: 'st',
    title: t('Active'),
    render: (data) => data
      ? `<span class="text-green-700">${t('Active')}</span>`
      : `<span class="text-gray-500">${t('Inactive')}</span>`,
  },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])

const stopSyncFilters = useQueryFilters(form, 'despesas.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head :title="$t('Expenses')" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">{{ $t('Expenses') }}</h2>
    <div class="flex gap-4">
      <Link :href="route('despesas.create')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>{{ $t('New Expense') }}
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-3 gap-2">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
      :placeholder="$t('Search by description/document')"
    >
    <select v-model="form.centro_custo_id" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('Cost Center') }}</option>
      <option v-for="c in props.centrosCusto" :key="c.id" :value="c.id">{{ c.codigo }} - {{ c.nome }}</option>
    </select>
    <select v-model="form.conta_contabil_id" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('Accounting Account') }}</option>
      <option v-for="c in props.contasContabeis" :key="c.id" :value="c.id">{{ c.codigo }} - {{ c.nome }}</option>
    </select>
    <select v-model="form.fornecedor_id" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('Supplier') }}</option>
      <option v-for="f in props.fornecedores" :key="f.id_fornecedor" :value="f.id_fornecedor">
        {{ f.nome_fornecedor || f.razao_social }}
      </option>
    </select>
    <select v-model="form.ativo" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('Active') }}</option>
      <option :value="1">{{ $t('Active') }}</option>
      <option :value="0">{{ $t('Inactive') }}</option>
    </select>
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500" :placeholder="$t('Start Date')">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500" :placeholder="$t('End Date')">
  </div>

  <DataTable
    table-id="dt-despesas"
    :ajax-url="route('despesas.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'desc']]"
    :page-length="10"
    :actions-col-index="7"
  />
</template>
