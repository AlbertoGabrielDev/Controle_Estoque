<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: Object,
})

const form = reactive({
  q: props.filters?.q ?? '',
  tipo: props.filters?.tipo ?? '',
  ativo: props.filters?.ativo ?? '',
})

const stopSyncFilters = useQueryFilters(form, 'itens.index')
onBeforeUnmount(() => stopSyncFilters())

const { t } = useI18n()

const dtColumns = computed(() => [
  { data: 'c1', title: t('SKU') },
  { data: 'c2', title: t('Name') },
  { data: 'c3', title: t('Type') },
  { data: 'c4', title: t('Category'), className: 'hidden lg:table-cell' },
  { data: 'c5', title: t('Unit'), className: 'hidden lg:table-cell' },
  { data: 'c6', title: t('Base Price') },
  {
    data: 'st',
    title: t('Active'),
    render: (data) => data
      ? `<span class="text-green-700">${t('Active')}</span>`
      : `<span class="text-gray-500">${t('Inactive')}</span>`,
  },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])
</script>

<template>
  <Head :title="$t('Items')" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">{{ $t('Items') }}</h2>
    <div class="flex gap-4">
      <Link :href="route('itens.create')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>{{ $t('New Item') }}
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-3 gap-2">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
      :placeholder="$t('Search by SKU or name')"
    >
    <select v-model="form.tipo" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('Type') }}</option>
      <option value="produto">{{ $t('Products') }}</option>
      <option value="servico">{{ $t('Service') }}</option>
    </select>
    <select v-model="form.ativo" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('Active') }}</option>
      <option :value="1">{{ $t('Active') }}</option>
      <option :value="0">{{ $t('Inactive') }}</option>
    </select>
  </div>

  <DataTable
    table-id="dt-itens"
    :ajax-url="route('itens.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'asc']]"
    :page-length="10"
    :actions-col-index="7"
  />
</template>
