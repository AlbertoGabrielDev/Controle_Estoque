<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import DataTable from '@/components/DataTable.vue'
import { useQueryFilters } from '@/composables/useQueryFilters'

const props = defineProps({ filters: { type: Object, default: () => ({}) } })
const form = reactive({ q: props.filters.q ?? '', tipo: props.filters.tipo ?? '' })
const { t } = useI18n()

const dtColumns = computed(() => [
  { data: 'id', title: '#', width: '60px' },
  { data: 'c1', title: t('Name') }, { data: 'c2', title: t('Type') },
  { data: 'c3', title: t('Max Discount %') }, { data: 'c4', title: t('Active') },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])

const stopSync = useQueryFilters(form, 'commercial.discount-policies.index')
onBeforeUnmount(() => stopSync())
</script>

<template>
  <Head :title="$t('Discount Policies')" />
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('Discount Policies') }}</h2>
    <Link :href="route('commercial.discount-policies.create')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-md text-gray-800 dark:text-slate-100 transition-colors">{{ $t('New Policy') }}</Link>
  </div>
  <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
    <input v-model="form.q" type="text" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" :placeholder="$t('Search...')">
    <select v-model="form.tipo" class="px-3 py-2 border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      <option value="">{{ $t('All Types') }}</option>
      <option value="item">{{ $t('Per Item') }}</option>
      <option value="pedido">{{ $t('Per Order') }}</option>
    </select>
  </div>
  <DataTable table-id="dt-commercial-discount-policies" :ajax-url="route('commercial.discount-policies.data')" :ajax-params="form" :columns="dtColumns" :order="[[0, 'desc']]" :page-length="15" :actions-col-index="5" />
</template>
