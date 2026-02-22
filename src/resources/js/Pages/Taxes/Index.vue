<script setup>
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
    case 1: return 'Percentual'
    case 2: return 'Valor fixo'
    case 3: return 'Fórmula'
    default: return '—'
  }
}

const dtColumns = [
  // usa data:null e combina c2 + c1 do row
  {
    data: null, title: 'Taxa',
    render: (d, type, row) => {
      const label = [row?.c2, row?.c1].filter(Boolean).join(' - ') || '—'
      if (type !== 'display') return label
      const url = route('taxes.edit', row?.id)
      return `<a href="${url}" class="text-blue-600 hover:underline">${esc(label)}</a>`
    }
  },
  { data: 'seg',  title: 'Segmento',   className: 'hidden lg:table-cell' },
  { data: 'ufo',  title: 'UF Origem' },
  { data: 'ufd',  title: 'UF Destino' },
  { data: 'can',  title: 'Canal',      className: 'hidden xl:table-cell' },
  { data: 'op',   title: 'Operação',   className: 'hidden xl:table-cell' },
  {
    data: 'met', title: 'Método',
    render: (v, t) => t === 'display' ? methodLabel(v) : v
  },
  {
    data: null, title: 'Valor Regra', orderable: false, searchable: false,
    render: (d, t, row)=> {
      if (t !== 'display') return row?.aliq ?? row?.vfx ?? ''
      const metodo = Number(row?.met)
      if (metodo === 2) {
        if (row?.vfx === null || row?.vfx === undefined || row?.vfx === '') return '—'
        return money(row.vfx)
      }
      if (metodo === 3) {
        return '<span class="text-indigo-600">Fórmula</span>'
      }
      if (row?.aliq === null || row?.aliq === undefined || row?.aliq === '') return '—'
      return `${Number(row.aliq).toFixed(2)}%`
    }
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
