<template>
  <div>
    <div date-rangepicker class="flex items-center">
      <!-- Input para Data de Início -->
      <div class="relative mr-2">
        <label for="start" class="block text-sm font-medium text-gray-700">Início</label>
        <input name="start" type="date" v-model="startDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date start">
      </div>

      <span class="text-gray-500 self-end mb-2" style="margin-left: -7px;">a</span>

      <!-- Input para Data de Fim -->
      <div class="relative">
        <label for="end" class="block text-sm font-medium text-gray-700">Fim</label>
        <input name="end" type="date" v-model="endDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date end">
      </div>
    </div>

    <button @click="fetchData" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Filtrar</button>

    <div class="chart-container">
      <component :is="chartType" :data="data" :options="options" ref="chartComponent" />
      <div class="text-center text-lg font-bold text-gray-900 mt-4">
        Soma: {{ totalSum }}
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  PointElement,
  Legend,
  BarElement,
  ArcElement,
  LineElement,
  CategoryScale,
  LinearScale,
  Plugin
} from 'chart.js'
import { Bar, Line, Doughnut } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, ArcElement, LineElement, Title, Tooltip, Legend)

// Plugin personalizado para mostrar o texto no centro do gráfico


export default {
  name: 'App',
  components: {
    Bar,
    Line,
    Doughnut
  },
  data() {
    return {
      chartType: 'Doughnut', // Defina o tipo de gráfico padrão
      startDate: '',
      endDate: '',
      data: {
        labels: [],
        datasets: [{
          data: [],
          backgroundColor: [] // Array para cores
        }]
      },
      totalSum: 0, // Inicialize totalSum
      options: {
        responsive: true,
        plugins: {
          centerText: {
            totalSum: this.totalSum // Inicialize totalSum nas opções do plugin
          }
        },
        elements: {
				center: {
					text: '90%',
          color: '#FF6384', // Default is #000000
          fontStyle: 'Arial', // Default is Arial
          sidePadding: 20 // Defualt is 20 (as a percentage)
				}
			}
      }
    }
  },
  created() {
    const now = new Date();
    const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
    const lastDayOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];

    this.startDate = firstDayOfMonth;
    this.endDate = lastDayOfMonth;

    this.fetchData();
  },
  methods: {
    fetchData() {
      let url = 'http://127.0.0.1:8000/verdurao/estoque/grafico-filtro'; // URL para buscar os dados

      if (this.startDate && this.endDate) {
        url += `?start_date=${this.startDate}&end_date=${this.endDate}`;
      }

      fetch(url)
      .then((response) => response.json())
      .then((fetchedData) => {
        this.data = {
          labels: fetchedData.labels,
          datasets: [{ 
            data: fetchedData.values,
            backgroundColor: this.generateRandomColors(fetchedData.labels.length)
          }]
        };
        this.totalSum = fetchedData.total_sum;
      });
    },
    // Função para gerar cores aleatórias
    generateRandomColors(count) {
      const colors = [];
      for (let i = 0; i < count; i++) {
        const color = '#' + Math.floor(Math.random() * 16777215).toString(16);
        colors.push(color);
      }
      return colors;
    }
  },
}

</script>

<style>
.chart-container {
  width: 800px;
  height: 600px;
}
</style>
