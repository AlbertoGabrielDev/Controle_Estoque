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
  { data: 'c1', title: t('Number') },
  { data: 'c2', title: t('Status') },
  { data: 'c3', title: t('Date') },
  { data: 'c4', title: t('Items') },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])

const stopSyncFilters = useQueryFilters(form, 'purchases.requisitions.index')
onBeforeUnmount(() => stopSyncFilters())
</script>

<template>
  <Head :title="$t('Requisitions')" />

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">{{ $t('Requisitions') }}</h2>
    <div class="flex gap-4">
      <Link :href="route('purchases.requisitions.create')" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        {{ $t('New Requisition') }}
      </Link>
    </div>
  </div>

  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-4 gap-2">
    <input
      v-model="form.q"
      type="text"
      class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
      :placeholder="$t('Search by number')"
    >
    <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
      <option value="">{{ $t('Status') }}</option>
      <option value="draft">{{ $t('Draft') }}</option>
      <option value="aprovado">{{ $t('Approved') }}</option>
      <option value="cancelado">{{ $t('Canceled') }}</option>
      <option value="fechado">{{ $t('Closed') }}</option>
    </select>
    <input v-model="form.data_inicio" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
    <input v-model="form.data_fim" type="date" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
  </div>

  <DataTable
    table-id="dt-purchases-requisitions"
    :ajax-url="route('purchases.requisitions.data')"
    :ajax-params="form"
    :columns="dtColumns"
    :order="[[0, 'desc']]"
    :page-length="10"
    :actions-col-index="4"
  />
</template>
