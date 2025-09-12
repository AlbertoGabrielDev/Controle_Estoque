<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
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
  daily: Object,      // {labels, totais, qtds}
  topProd: Object,    // {labels, totais, qtds}
  byStatus: Object,   // {labels, totais}
  monthly: Object,    // {labels, totais, year}
  unidade: String
})

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

// >>> legenda no topo e altura menor do card
const lineOptions = {
  responsive: true,
  maintainAspectRatio: false,
  layout: { padding: { top: 4, right: 8, bottom: 16, left: 8 } },
  plugins: {
    legend: { position: 'top', labels: { boxWidth: 12, padding: 8 } }, // <- topo
    title: { display: false }
  },
  scales: {
    x: {
      grid: { display: true },
      ticks: { autoSkip: true, maxTicksLimit: 10 }
    },
    y:  { beginAtZero: true, position: 'left'  },
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
        'rgba(59,130,246,0.7)','rgba(16,185,129,0.7)','rgba(234,179,8,0.7)',
        'rgba(239,68,68,0.7)','rgba(99,102,241,0.7)'
      ]
    }
  ]
}))
const barOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }

const doughnutData = computed(() => ({
  labels: props.byStatus.labels,
  datasets: [{
    data: props.byStatus.totais,
    backgroundColor: ['#60a5fa','#34d399','#fbbf24','#a78bfa','#f87171'],
    borderWidth: 0
  }]
}))
const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  layout: { padding: { top: 4, right: 8, bottom: 32, left: 8 } },
  plugins: {
    legend: { position: 'top', labels: { boxWidth: 12, padding: 8 } },
    title: { display: false }
  },
  cutout: '70%',
  radius: '72%'
}

const monthlyData = computed(() => ({
  labels: props.monthly.labels,
  datasets: [{
    label: `Faturamento ${props.monthly.year} (R$)`,
    data: props.monthly.totais,
    backgroundColor: 'rgba(99,102,241,0.7)'
  }]
}))
const monthlyOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
</script>

<template>
  <Head title="Dashboard de Vendas" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl md:text-3xl font-bold">Dashboard de Vendas</h1>
      <div v-if="unidade" class="text-sm text-gray-500">Unidade: <span class="font-medium">{{ unidade }}</span></div>
    </div>

    <!-- Linha 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- altura reduzida de h-80 para h-64 -->
      <div class="lg:col-span-2 bg-white rounded-xl shadow p-4 pb-6 h-60">
        <div class="font-semibold mb-2">Vendas diárias (30 dias)</div>
        <Line :data="lineData" :options="lineOptions" />
      </div>

      <div class="bg-white rounded-xl shadow p-4 h-80">
        <div class="font-semibold mb-2">Pedidos por status</div>
        <Doughnut :data="doughnutData" :options="doughnutOptions" />
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
