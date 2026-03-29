<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

import { Head, Link } from '@inertiajs/vue3'
import DataTable, { esc } from '@/components/DataTable.vue'

function fmtDate(iso) {
  if (!iso) return '—'
  const [y,m,d] = String(iso).split('-')
  return d && m && y ? `${d}/${m}/${y}` : iso
}

function money(v) {
  return Number(v ?? 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function methodLabel(value) {
  switch (Number(value)) {
    case 1: return t('Percentage')
    case 2: return t('Fixed value')
    case 3: return t('Formula')
    default: return '—'
  }
}

import { computed } from 'vue'

const dtColumns = computed(() => [
  // usa data:null e combina c2 + c1 do row
  {
    data: null, title: t('Tax'),
    render: (d, type, row) => {
      const label = [row?.c2, row?.c1].filter(Boolean).join(' - ') || '—'
      if (type !== 'display') return label
      const url = route('taxes.edit', row?.id)
      return `<a href="${url}" class="text-blue-600 hover:underline">${esc(label)}</a>`
    }
  },
  { data: 'seg',  title: t('Segment'),   className: 'hidden lg:table-cell' },
  { data: 'ufo',  title: t('Origin UF') },
  { data: 'ufd',  title: t('Dest UF') },
  { data: 'can',  title: t('Channel'),      className: 'hidden xl:table-cell' },
  { data: 'op',   title: t('Operation'),   className: 'hidden xl:table-cell' },
  {
    data: 'met', title: t('Method'),
    render: (v, type) => type === 'display' ? methodLabel(v) : v
  },
  {
    data: null, title: t('Rule Value'), orderable: false, searchable: false,
    render: (d, type, row)=> {
      if (type !== 'display') return row?.aliq ?? row?.vfx ?? ''
      const metodo = Number(row?.met)
      if (metodo === 2) {
        if (row?.vfx === null || row?.vfx === undefined || row?.vfx === '') return '—'
        return money(row.vfx)
      }
      if (metodo === 3) {
        return `<span class="text-indigo-600">${t('Formula')}</span>`
      }
      if (row?.aliq === null || row?.aliq === undefined || row?.aliq === '') return '—'
      return `${Number(row.aliq).toFixed(2)}%`
    }
  },
  { data: 'prio', title: t('Priority') },
  {
    data: 'cum',  title: t('Cumulative'),
    render: (v, type)=> type==='display' ? (v ? `<span class="text-green-700">${t('Yes')}</span>` : `<span class="text-gray-500">${t('No')}</span>`) : v
  },
  {
    data: null, title: t('Validity'), defaultContent: '',
    render: (d, type, row)=> type==='display' ? `${fmtDate(row?.vi)} — ${fmtDate(row?.vf)}` : `${row?.vi}|${row?.vf}`
  },
  { data: 'acoes', title: t('Actions'), orderable:false, searchable:false },
])
</script>


<template>
  <Head :title="$t('Tax Rules')" />
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">{{ $t('Tax Rules') }}</h2>
    <Link :href="route('taxes.create')" class="px-3 py-2 rounded bg-blue-600 text-white">{{ $t('New Rule') }}</Link>
  </div>

  <DataTable
    table-id="dt-taxes"
    :enhance-only="false"
    :ajax-url="route('taxes.data')"
    :ajax-params="{}"
    :order="[[0,'asc']]"
    :page-length="10"
    :columns="dtColumns"
  />
</template>
