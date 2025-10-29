<script setup>
import { Head, Link } from '@inertiajs/vue3'
import DataTable from '@/components/DataTable.vue'

function fmtDate(iso) {
  if (!iso) return '—'
  const [y,m,d] = String(iso).split('-')
  return d && m && y ? `${d}/${m}/${y}` : iso
}

const dtColumns = [
  // usa data:null e combina c2 + c1 do row
  {
    data: null, title: 'Taxa',
    render: (d, type, row) => {
      const label = [row?.c2, row?.c1].filter(Boolean).join(' - ') || '—'
      if (type !== 'display') return label
      const url = route('taxes.edit', row?.id)
      return `<a href="${url}" class="text-blue-600 hover:underline">${label}</a>`
    }
  },
  { data: 'seg',  title: 'Segmento',   className: 'hidden lg:table-cell' },
  { data: 'ufo',  title: 'UF Origem' },
  { data: 'ufd',  title: 'UF Destino' },
  { data: 'can',  title: 'Canal',      className: 'hidden xl:table-cell' },
  { data: 'op',   title: 'Operação',   className: 'hidden xl:table-cell' },
  {
    data: 'aliq', title: '% Alíquota',
    render: (v,t)=> t==='display' ? `${(Number(v ?? 0)).toFixed(2)}%` : v
  },
  { data: 'prio', title: 'Prioridade' },
  {
    data: 'cum',  title: 'Cumulativo',
    render: (v,t)=> t==='display' ? (v ? '<span class="text-green-700">Sim</span>' : '<span class="text-gray-500">Não</span>') : v
  },
  {
    data: null, title: 'Vigência', defaultContent: '',
    render: (d,t,row)=> t==='display' ? `${fmtDate(row?.vi)} — ${fmtDate(row?.vf)}` : `${row?.vi}|${row?.vf}`
  },
  { data: 'acoes', title: 'Ações', orderable:false, searchable:false },
]
</script>


<template>
  <Head title="Regras de Taxa" />
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Regras de Taxa</h2>
    <Link :href="route('taxes.create')" class="px-3 py-2 rounded bg-blue-600 text-white">Nova Regra</Link>
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
