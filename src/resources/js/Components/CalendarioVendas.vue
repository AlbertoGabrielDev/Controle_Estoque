<template>
  <div class="w-full mx-auto max-w-6xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
      <div class="flex items-center gap-2">
        <button class="px-3 py-2 rounded-lg border hover:bg-gray-50"
                @click="goPrevMonth" aria-label="Mês anterior">
          ‹
        </button>
        <div class="text-lg font-semibold min-w-[12ch] text-center">
          {{ monthLabel }} de {{ currentYear }}
        </div>
        <button class="px-3 py-2 rounded-lg border hover:bg-gray-50"
                @click="goNextMonth" aria-label="Próximo mês">
          ›
        </button>
      </div>

      <div class="flex items-center gap-2">
        <button class="px-3 py-2 rounded-lg border hover:bg-gray-50"
                @click="goToday">
          Hoje
        </button>
      </div>
    </div>

    <div class="grid grid-cols-7 text-xs sm:text-sm text-gray-500 mb-1 select-none">
      <div v-for="d in weekDays" :key="d" class="py-2 text-center font-medium">
        {{ d }}
      </div>
    </div>

    <div class="grid grid-cols-7 gap-1 sm:gap-2">
      <div v-for="cell in calendarCells" :key="cell.key"
           class="border rounded-lg p-2 sm:p-3 min-h-[92px] relative overflow-hidden cursor-pointer"
           :class="[
             !cell.inCurrentMonth ? 'bg-gray-50 text-gray-400' : 'bg-white',
             isToday(cell.date) ? 'ring-2 ring-blue-500' : ''
           ]"
           @click="openDay(cell)">
        <div class="flex items-center justify-between">
          <div class="text-sm sm:text-base font-semibold">
            {{ cell.date.getDate() }}
          </div>
          <div v-if="cell.stats.totalQty > 0"
               class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full border">
            {{ cell.stats.totalQty }} unid.
          </div>
        </div>

        <ul v-if="cell.stats.products.length" class="mt-1 space-y-0.5 text-[11px] sm:text-xs leading-tight">
          <li v-for="(p, idx) in cell.stats.products.slice(0,2)" :key="idx" class="truncate">
            • {{ p.name }} ({{ p.qty }})
          </li>
          <li v-if="cell.stats.products.length > 2" class="text-gray-500">
            + {{ cell.stats.products.length - 2 }} mais
          </li>
        </ul>

        <div v-if="cell.stats.totalQty > 0"
             class="absolute bottom-1 right-2 text-[10px] sm:text-xs text-blue-600">
          Detalhes ⇢
        </div>
      </div>
    </div>

    <div v-if="modal.open" class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4"
         @click.self="modal.open = false">
      <div class="bg-white w-full max-w-3xl rounded-xl shadow-xl p-4 sm:p-6">
        <div class="flex items-center justify-between gap-2">
          <h3 class="text-lg font-semibold">
            {{ modal.title }}
          </h3>
          <button class="px-3 py-2 rounded-lg border hover:bg-gray-50" @click="modal.open = false">
            Fechar
          </button>
        </div>

        <div class="mt-2 text-sm text-gray-600 flex flex-wrap items-center gap-2">
          <span>Total de unidades:</span>
          <span class="font-semibold text-gray-800">{{ modal.stats.totalQty }}</span>
          <span class="mx-2 hidden sm:inline">•</span>
          <span>Registros:</span>
          <span class="font-semibold text-gray-800">{{ modal.details.length }}</span>
        </div>

        <div class="mt-3">
          <input v-model="productFilter" type="text" placeholder="Filtrar por produto..."
                 class="w-full border rounded-lg px-3 py-2 text-sm"
                 @keydown.esc="productFilter = ''">
        </div>

        <div class="mt-3 max-h-[60vh] overflow-auto">
          <table class="w-full text-sm">
            <thead class="sticky top-0 bg-white">
              <tr class="text-left border-b">
                <th class="py-2 pr-2">Hora</th>
                <th class="py-2 pr-2">Produto</th>
                <th class="py-2 px-2 text-right">Qtd</th>
                <th class="py-2 pl-2 text-right">Unidade</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(r, idx) in filteredRows" :key="idx" class="border-b">
                <td class="py-2 pr-2 whitespace-nowrap">{{ r.time }}</td>
                <td class="py-2 pr-2">{{ r.product }}</td>
                <td class="py-2 px-2 text-right">{{ r.quantity }}</td>
                <td class="py-2 pl-2 text-right">{{ r.unit }}</td>
              </tr>
              <tr v-if="filteredRows.length === 0">
                <td class="py-8 text-center text-gray-500" colspan="4">Nada encontrado…</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-4 flex items-center justify-end gap-2">
          <button class="px-3 py-2 rounded-lg border hover:bg-gray-50"
                  @click="exportCsvDia">
            Exportar CSV
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="fixed bottom-3 right-3 bg-white border rounded-lg shadow px-3 py-2 text-sm z-30">
      Carregando dados…
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
const props = defineProps({
  salesByDate: { type: Object, default: () => ({}) },
  initialYear: { type: Number, default: null },
  initialMonth: { type: Number, default: null }, // 1-12
  fetchEndpoint: { type: String, default: '' },
  locale: { type: String, default: 'pt-BR' },
})

/** ESTADO DE DATA */
const today = new Date()
const currentYear = ref(props.initialYear || today.getFullYear())
const currentMonth = ref(props.initialMonth || (today.getMonth() + 1)) 

/** DADOS */
const loading = ref(false)
const data = reactive({
  salesByDate: normalizeMap(props.salesByDate)
})

watch(() => props.salesByDate, (nv) => {
  data.salesByDate = normalizeMap(nv || {})
})

/** Nome do mês e dias da semana */
const monthLabel = computed(() => {
  const date = new Date(currentYear.value, currentMonth.value - 1, 1)
  return new Intl.DateTimeFormat(props.locale, { month: 'long' }).format(date)
    .replace(/^./, c => c.toUpperCase())
})

const weekDays = computed(() => ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'])

/** CÁLCULO DA GRADE */
const calendarCells = computed(() => {
  const y = currentYear.value
  const m = currentMonth.value
  const firstOfMonth = new Date(y, m - 1, 1)
  const lastOfMonth = new Date(y, m, 0)
  const daysInMonth = lastOfMonth.getDate()

  const firstWeekday = toMondayIndex(firstOfMonth.getDay())
  const prevMonthLast = new Date(y, m - 1, 0)
  const prevDays = firstWeekday

  const cells = []

  for (let i = prevDays - 1; i >= 0; i--) {
    const d = new Date(y, m - 2, prevMonthLast.getDate() - i)
    cells.push(buildCell(d, false))
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const d = new Date(y, m - 1, day)
    cells.push(buildCell(d, true))
  }

  while (cells.length % 7 !== 0) {
    const last = cells[cells.length - 1].date
    const d = new Date(last)
    d.setDate(d.getDate() + 1)
    cells.push(buildCell(d, false))
  }

  return cells
})

/** MODAL */
const modal = reactive({
  open: false,
  title: '',
  dateKey: '',
  stats: {
    totalQty: 0,
    products: [] // {name, qty}
  },
  details: [] // [{time, product, unit, quantity}]
})

const productFilter = ref('')
const filteredRows = computed(() => {
  const rows = modal.details
  if (!productFilter.value.trim()) return rows
  const q = productFilter.value.toLowerCase()
  return rows.filter(r => r.product.toLowerCase().includes(q))
})

/** WATCH para carregamento “lazy” por mês (opcional) */
watch([currentYear, currentMonth], async () => {
  if (!props.fetchEndpoint) return
  await fetchMonthData()
}, { immediate: !!props.fetchEndpoint })

/** FUNÇÕES PRINCIPAIS */
function goPrevMonth() {
  let y = currentYear.value
  let m = currentMonth.value - 1
  if (m < 1) { m = 12; y-- }
  currentYear.value = y
  currentMonth.value = m
}

function goNextMonth() {
  let y = currentYear.value
  let m = currentMonth.value + 1
  if (m > 12) { m = 1; y++ }
  currentYear.value = y
  currentMonth.value = m
}

function goToday() {
  currentYear.value = today.getFullYear()
  currentMonth.value = today.getMonth() + 1
}

function openDay(cell) {
  const k = toKey(cell.date)
  const rows = (data.salesByDate[k] || []).slice().sort((a, b) => (a.time || '').localeCompare(b.time || ''))
  modal.open = true
  modal.title = formatDateHuman(cell.date, props.locale)
  modal.dateKey = k
  modal.details = rows
  modal.stats = aggregate(rows)
  productFilter.value = ''
}

function isToday(d) {
  return d.getFullYear() === today.getFullYear() &&
         d.getMonth() === today.getMonth() &&
         d.getDate() === today.getDate()
}

/** Auxiliares de célula/estatística */
function buildCell(date, inCurrentMonth) {
  const key = toKey(date)
  const rows = data.salesByDate[key] || []
  const stats = aggregate(rows)
  return { date, inCurrentMonth, key, stats }
}

function aggregate(items) {
  // items: [{time, product, unit, quantity}]
  const map = new Map()
  let totalQty = 0

  for (const it of items) {
    const name = (it.product || it.nome_produto || it.name || '').toString()
    const qty = Number(it.quantity ?? it.quantidade ?? it.qty ?? 0)
    totalQty += qty

    const key = name.trim().toLowerCase()
    if (!map.has(key)) map.set(key, { name, qty: 0 })
    map.get(key).qty += qty
  }

  const products = Array.from(map.values())
    .sort((a, b) => b.qty - a.qty || a.name.localeCompare(b.name))

  return { totalQty, products }
}

function toKey(date) {
  const y = date.getFullYear()
  const m = (date.getMonth() + 1).toString().padStart(2, '0')
  const d = date.getDate().toString().padStart(2, '0')
  return `${y}-${m}-${d}`
}

function toMondayIndex(jsDay) {
  // JS: 0=Dom … 6=Sáb => queremos 0=Seg … 6=Dom
  return (jsDay + 6) % 7
}

function normalizeMap(obj) {
  const out = {}
  for (const k of Object.keys(obj || {})) {
    out[k] = Array.isArray(obj[k]) ? obj[k] : []
  }
  return out
}

function formatDateHuman(date, locale) {
  const df = new Intl.DateTimeFormat(locale || 'pt-BR', {
    weekday: 'long', day: '2-digit', month: 'long', year: 'numeric'
  })
  const s = df.format(date)
  return s.replace(/^./, c => c.toUpperCase())
}

/** Carregar dados do mês, se fetchEndpoint informado */
async function fetchMonthData() {
  loading.value = true
  try {
    const url = new URL(props.fetchEndpoint, window.location.origin)
    url.searchParams.set('year', String(currentYear.value))
    url.searchParams.set('month', String(currentMonth.value)) // 1-12

    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const json = await res.json()
    // Esperado: { salesByDate: { 'YYYY-MM-DD': [{time, product, unit, quantity}] } }
    data.salesByDate = normalizeMap(json.salesByDate || {})
  } catch (e) {
    console.error('Falha ao carregar dados do mês:', e)
  } finally {
    loading.value = false
  }
}

/** Exportar CSV do dia aberto */
function exportCsvDia() {
  const rows = [
    ['Hora', 'Produto', 'Quantidade', 'Unidade'],
    ...modal.details.map(r => [r.time ?? '', r.product ?? '', String(r.quantity ?? 0), r.unit ?? ''])
  ]
  const csv = rows.map(r => r.map(escapeCsv).join(';')).join('\n')
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const a = document.createElement('a')
  a.href = URL.createObjectURL(blob)
  a.download = `vendas_${modal.dateKey}.csv`
  a.click()
  URL.revokeObjectURL(a.href)
}

function escapeCsv(v) {
  const s = String(v ?? '')
  if (/[;"\n]/.test(s)) return `"${s.replace(/"/g, '""')}"`
  return s
}
</script>

<style scoped>
/* Fallback mínimo caso não use Tailwind */
:where(.border){ border:1px solid rgba(0,0,0,.08) }
:where(.rounded-lg){ border-radius:.5rem }
:where(.rounded-xl){ border-radius:.75rem }
:where(.shadow){ box-shadow:0 1px 3px rgba(0,0,0,.08) }
:where(.shadow-xl){ box-shadow:0 8px 30px rgba(0,0,0,.12) }
:where(.ring-2){ outline:2px solid; outline-offset:2px }
:where(.ring-blue-500){ outline-color:#3b82f6 }
:where(.hover\:bg-gray-50:hover){ background:#f9fafb }
:where(.bg-white){ background:#fff }
:where(.bg-gray-50){ background:#f9fafb }
</style>
