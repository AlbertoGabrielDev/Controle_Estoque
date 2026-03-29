<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive, ref, computed } from 'vue'
import DataTable, { linkify } from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps({
  filters: Object,
})

const filterOpen = ref(false)

const form = reactive({
  q: props.filters?.q ?? '',
  status: props.filters?.status ?? '',
  cod_produto: props.filters?.cod_produto ?? '',
  nome_produto: props.filters?.nome_produto ?? '',
  nome_fornecedor: props.filters?.nome_fornecedor ?? '',
  nome_marca: props.filters?.nome_marca ?? '',
  lote: props.filters?.lote ?? '',
  localizacao: props.filters?.localizacao ?? '',
  quantidade: props.filters?.quantidade ?? '',
  preco_custo: props.filters?.preco_custo ?? '',
  preco_venda: props.filters?.preco_venda ?? '',
  validade: props.filters?.validade ?? '',
  data_chegada: props.filters?.data_chegada ?? '',
  nome_categoria: props.filters?.nome_categoria ?? '',
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

function money(data) {
  return Number(data || 0).toLocaleString(currentLocale.value, { 
    style: 'currency', 
    currency: currencySymbol.value 
  })
}

function formatDate(data) {
  if (!data) return '—'
  const dt = new Date(data)
  if (Number.isNaN(dt.getTime())) return data
  return dt.toLocaleDateString(currentLocale.value)
}

const dtColumns = computed(() => [
  { data: 'c1', title: t('Code') },
  linkify({ data: 'c2', title: t('Product') }, { routeName: 'estoque.editar', idField: 'id' }),
  { data: 'c10', title: t('Brand'), className: 'hidden lg:table-cell' },
  { data: 'c4', title: t('Cost'), render: (data) => money(data), className: 'hidden md:table-cell' },
  { data: 'c5', title: t('Sale'), render: (data) => money(data), className: 'hidden md:table-cell' },
  { data: 'c6', title: t('Qty') },
  { data: 'c7', title: t('Lot') },
  { data: 'c8', title: t('Location'), className: 'hidden md:table-cell' },
  { data: 'c9', title: t('Expiration'), render: (data) => formatDate(data) },
  {
    data: 'st',
    title: t('Status'),
    render: (data) => data
      ? `<span class="text-green-700">${t('Active')}</span>`
      : `<span class="text-gray-500">${t('Inactive')}</span>`,
  },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])

const stopSyncFilters = useQueryFilters(form, 'estoque.index', { debounce: 250 })
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head :title="$t('Stock')" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">{{ $t('Stock') }}</h2>
    <div class="flex gap-4">
      <Link :href="route('estoque.historico')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-clock-rotate-left mr-2"></i>{{ $t('History') }}
      </Link>
      <Link :href="route('estoque.cadastro')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>{{ $t('Register Stock') }}
      </Link>
    </div>
  </div>

  <div class="mb-4">
    <button
      type="button"
      class="w-full md:w-auto flex items-center justify-between px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md"
      @click="filterOpen = !filterOpen"
    >
      <span class="mr-2">{{ $t('Filter') }}</span>
      <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': filterOpen }"></i>
    </button>

    <div v-show="filterOpen" class="mt-4 p-4 bg-gray-50 rounded-lg">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <input v-model="form.cod_produto" type="text" :placeholder="$t('Code')" class="px-3 py-2 border rounded-md">
        <input v-model="form.nome_produto" type="text" :placeholder="$t('Product')" class="px-3 py-2 border rounded-md">
        <input v-model="form.nome_fornecedor" type="text" :placeholder="$t('Supplier')" class="px-3 py-2 border rounded-md">
        <input v-model="form.nome_marca" type="text" :placeholder="$t('Brand')" class="px-3 py-2 border rounded-md">
        <input v-model="form.nome_categoria" type="text" :placeholder="$t('Category')" class="px-3 py-2 border rounded-md">
        <input v-model="form.lote" type="text" :placeholder="$t('Lot')" class="px-3 py-2 border rounded-md">
        <input v-model="form.localizacao" type="text" :placeholder="$t('Location')" class="px-3 py-2 border rounded-md">
        <input v-model="form.quantidade" type="number" :placeholder="$t('Qty')" class="px-3 py-2 border rounded-md">
        <input v-model="form.preco_custo" type="number" step="0.01" :placeholder="$t('Cost')" class="px-3 py-2 border rounded-md">
        <input v-model="form.preco_venda" type="number" step="0.01" :placeholder="$t('Sale')" class="px-3 py-2 border rounded-md">
        <input v-model="form.validade" type="date" class="px-3 py-2 border rounded-md">
        <input v-model="form.data_chegada" type="date" class="px-3 py-2 border rounded-md">
        <select v-model="form.status" class="px-3 py-2 border rounded-md">
          <option value="">{{ $t('Status') }}</option>
          <option :value="1">{{ $t('Active') }}</option>
          <option :value="0">{{ $t('Inactive') }}</option>
        </select>
        <input
          v-model="form.q"
          type="text"
          :placeholder="$t('Global Search')"
          class="px-3 py-2 border rounded-md lg:col-span-2"
        >
      </div>
    </div>
  </div>

  <DataTable
    table-id="dt-estoque"
    :ajax-url="route('estoque.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'desc']]"
    :page-length="10"
    :actions-col-index="10"
  />
</template>
