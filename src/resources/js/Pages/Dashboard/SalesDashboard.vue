<script setup>
import { computed, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import {
  Chart as ChartJS,
  Title, Tooltip, Legend,
  LineElement, BarElement, PointElement, ArcElement,
  CategoryScale, LinearScale
} from 'chart.js'
import { Line, Bar, Doughnut } from 'vue-chartjs'

ChartJS.register(
  Title, Tooltip, Legend,
  LineElement, BarElement, PointElement, ArcElement,
  CategoryScale, LinearScale
)

const props = defineProps({
  daily: Object,
  topProd: Object,
  byStatus: Object,
  monthly: Object,
  unidade: String,
  byUnit: Object,
  kpis: Object,
  attendants: Array,
  attendantId: Number,
  from: String, // yyyy-mm-dd
  to: String    // yyyy-mm-dd
})

const DASHBOARD_ROUTE_NAME = 'dashboard.index'

// Filtros (atendente + intervalo)
const selectedAtt = ref(props.attendantId ?? '')
const fromDate = ref(props.from ?? '')
const toDate = ref(props.to ?? '')

// Util: aplica filtros via Inertia GET preservando o estado da página
function applyFilters(extra = {}) {
  const params = {
    atendente: selectedAtt.value || undefined,
    from: fromDate.value || undefined,
    to: toDate.value || undefined,
    ...extra
  }
  router.get(route(DASHBOARD_ROUTE_NAME), params, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

// Mudança de atendente aplica filtros mantendo data atual
watch(selectedAtt, () => applyFilters())

// Presets de intervalo
function setPreset(days) {
  // days: 7, 30, etc. Define from = hoje - (days-1) e to = hoje
  const today = new Date()
  const to = today.toISOString().slice(0, 10)
  const start = new Date(today)
  start.setDate(today.getDate() - (days - 1))
  const from = start.toISOString().slice(0, 10)
  fromDate.value = from
  toDate.value = to
  applyFilters()
}

function setThisMonth() {
  const today = new Date()
  const y = today.getFullYear()
  const m = today.getMonth()
  const from = new Date(y, m, 1).toISOString().slice(0, 10)
  const to = new Date(y, m + 1, 0).toISOString().slice(0, 10)
  fromDate.value = from
  toDate.value = to
  applyFilters()
}

function clearDates() {
  fromDate.value = ''
  toDate.value = ''
  applyFilters()
}

// Dados/Opções dos gráficos
const lineData = computed(() => ({
  labels: props.daily.labels,
  datasets: [
    {
      label: 'Faturamento (R$)',
      data: props.daily.totais,
      borderWidth: 2,
      borderColor: 'rgba(59,130,246,0.8)',
      backgroundColor: 'rgba(59,130,246,0.15)',
      tension: 0.25,
      yAxisID: 'y'
    },
    {
      label: 'Quantidade',
      data: props.daily.qtds,
      borderWidth: 2,
      borderColor: 'rgba(16,185,129,0.8)',
      backgroundColor: 'rgba(16,185,129,0.15)',
      type: 'line',
      tension: 0.25,
      yAxisID: 'y1'
    }
  ]
}))

const lineOptions = {
  responsive: true,
  maintainAspectRatio: false,
  layout: { padding: { top: 4, right: 8, bottom: 16, left: 8 } },
  plugins: {
    legend: { position: 'top', labels: { boxWidth: 12, padding: 8 } },
    title: { display: false }
  },
  scales: {
    x: {
      grid: { display: true },
      ticks: { autoSkip: true, maxTicksLimit: 10 }
    },
    y: { beginAtZero: true, position: 'left' },
    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
  }
}

const barTopData = computed(() => ({
  labels: props.topProd.labels,
  datasets: [
    {
      label: 'Faturamento (R$)',
      data: props.topProd.totais,
      backgroundColor: [
        'rgba(59,130,246,0.7)', 'rgba(16,185,129,0.7)', 'rgba(234,179,8,0.7)',
        'rgba(239,68,68,0.7)', 'rgba(99,102,241,0.7)'
      ]
    }
  ]
}))
const barOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
const brl = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })
const doughnutData = computed(() => ({
  labels: props.byStatus.labels,
  datasets: [{
    data: props.byStatus.totais,
    backgroundColor: ['#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f87171'],
    borderWidth: 0
  }]
}))
const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  layout: { padding: { top: 4, right: 6, bottom: 16, left: 6 } },
  plugins: {
    legend: { position: 'top', labels: { boxWidth: 12, padding: 8 } },
    title: { display: false }
  },
  cutout: '70%',
  radius: '72%'
}

const byUnitData = computed(() => ({
  labels: props.byUnit.labels,
  datasets: [{
    label: 'Faturamento por Unidade (R$)',
    data: props.byUnit.totais,
    backgroundColor: 'rgba(14,165,233,0.75)',
  }]
}))
const byUnitOptions = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y',
  plugins: { legend: { display: false } },
  scales: {
    x: { beginAtZero: true },
    y: { ticks: { autoSkip: false } }
  }
}

const monthlyData = computed(() => ({
  labels: props.monthly.labels,
  datasets: [{
    label: `Faturamento ${props.monthly.year ?? ''} (R$)`,
    data: props.monthly.totais,
    backgroundColor: 'rgba(99,102,241,0.7)'
  }]
}))
const monthlyOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
</script>

<template>

  <Head title="Dashboard de Vendas" />

  <div class="space-y-6 dashboard-view">
    <!-- Filtros -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div class="flex items-center gap-2">
        <label for="att" class="text-sm text-gray-600">Usuario:</label>
        <select id="att" v-model="selectedAtt" class="border rounded-lg px-3 py-2 text-sm">
          <option :value="''">Todos</option>
          <option v-for="a in attendants" :key="a.id" :value="a.id">
            {{ a.name }}
          </option>
        </select>
      </div>

      <div class="flex items-end gap-3 flex-wrap">
        <div class="flex flex-col">
          <label class="text-sm text-gray-600">De</label>
          <input type="date" v-model="fromDate" class="border rounded-lg px-3 py-2 text-sm" />
        </div>
        <div class="flex flex-col">
          <label class="text-sm text-gray-600">Até</label>
          <input type="date" v-model="toDate" class="border rounded-lg px-3 py-2 text-sm" />
        </div>

        <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg px-4 py-2" @click="applyFilters()">
          Aplicar
        </button>

        <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg px-3 py-2" @click="clearDates">
          Limpar
        </button>
      </div>
    </div>

    <!-- Presets -->
    <div class="flex flex-wrap gap-2">
      <span class="text-sm text-gray-500">Atalhos:</span>
      <button class="px-3 py-1.5 text-sm rounded bg-gray-100 hover:bg-gray-200" @click="setPreset(7)">Últimos 7
        dias</button>
      <button class="px-3 py-1.5 text-sm rounded bg-gray-100 hover:bg-gray-200" @click="setPreset(30)">Últimos 30
        dias</button>
      <button class="px-3 py-1.5 text-sm rounded bg-gray-100 hover:bg-gray-200" @click="setThisMonth()">Este
        mês</button>
      <button class="px-3 py-1.5 text-sm rounded bg-gray-100 hover:bg-gray-200" @click="setPreset(1)">Hoje</button>
    </div>

    <!-- Cabeçalho -->
    <div class="flex items-center justify-between">
      <h1 class="text-2xl md:text-3xl font-bold">Dashboard de Vendas</h1>
      <div v-if="unidade" class="text-sm text-gray-500">
        Unidade: <span class="font-medium">{{ unidade }}</span>
      </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Vendas (intervalo)</div>
        <div class="text-xs text-gray-400">Contagem de vendas no intervalo</div>
        <div class="mt-1 text-2xl font-bold">{{ props.kpis?.salesCount ?? 0 }}</div>
      </div>

      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Faturamento <strong>bruto</strong></div>
        <div class="text-xs text-gray-400">Soma(preco_venda * quantidade)</div>
        <div class="mt-1 text-2xl font-bold">
          {{ brl.format(props.kpis?.grossRevenue ?? 0) }}
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Faturamento <strong>líquido</strong></div>
        <div class="text-xs text-gray-400">Bruto - devolucoes - descontos - impostos</div>
        <div class="mt-1 text-2xl font-bold">
          {{ brl.format(props.kpis?.netRevenue ?? 0) }}
        </div>
        <div class="text-xs text-gray-500 mt-1">
          Impostos: {{ brl.format(props.kpis?.taxes ?? 0) }}
        </div>
      </div>

      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Lucro (intervalo)</div>
        <div class="text-xs text-gray-400">(preco_venda - custo_unit) * qtd</div>
        <div class="mt-1 text-2xl font-bold">
          {{ brl.format(props.kpis?.profit ?? 0) }}
        </div>
      </div>
    </div>

    <!-- Linha 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">
      <!-- Vendas diárias -->
      <div class="lg:col-span-2 bg-white rounded-xl shadow p-4 pb-6 h-52">
        <div class="font-semibold mb-2">Vendas diárias</div>
        <Line :data="lineData" :options="lineOptions" />
      </div>

      <!-- Pedidos por status -->
      <div class="lg:col-span-2 bg-white rounded-xl shadow p-4 h-64">
        <div class="font-semibold mb-2">Pedidos por status</div>
        <Doughnut :data="doughnutData" :options="doughnutOptions" />
      </div>

      <!-- Vendas por unidade -->
      <div class="lg:col-span-2 bg-white rounded-xl shadow p-4 h-64">
        <div class="font-semibold mb-2">Vendas por unidade</div>
        <Bar :data="byUnitData" :options="byUnitOptions" />
      </div>
    </div>

    <!-- Linha 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl shadow p-4 h-80">
        <div class="font-semibold mb-2">Top 5 produtos (faturamento)</div>
        <Bar :data="barTopData" :options="barOptions" />
      </div>

      <div class="bg-white rounded-xl shadow p-4 h-80">
        <div class="font-semibold mb-2">Faturamento mensal</div>
        <Bar :data="monthlyData" :options="monthlyOptions" />
      </div>
    </div>
  </div>
</template>
