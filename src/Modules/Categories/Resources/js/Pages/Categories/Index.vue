<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { onBeforeUnmount, reactive, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({
  filters: Object,
})

const form = reactive({
  q: props.filters?.q ?? '',
  status: props.filters?.status ?? '',
})

const stopSyncFilters = useQueryFilters(form, 'categoria.index')
onBeforeUnmount(() => stopSyncFilters())

const { t } = useI18n()

const dtColumns = computed(() => [
  { data: 'c1', title: t('Code') },
  { data: 'c2', title: t('Category') },
  { data: 'c3', title: t('Type') },
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
  <Head :title="$t('Categories')" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">{{ $t('Categories') }}</h2>
    <div class="flex gap-4">
      <Link :href="route('categoria.inicio')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-angle-left mr-2"></i>{{ $t('Back') }}
      </Link>
      <Link :href="route('categoria.cadastro')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>{{ $t('Create') }}
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
      :placeholder="$t('Search by code or name')"
    >
    <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('Active') }}</option>
      <option :value="1">{{ $t('Active') }}</option>
      <option :value="0">{{ $t('Inactive') }}</option>
    </select>
  </div>

  <DataTable
    table-id="dt-categorias"
    :ajax-url="route('categoria.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'asc']]"
    :page-length="10"
    :actions-col-index="4"
  />
</template>
