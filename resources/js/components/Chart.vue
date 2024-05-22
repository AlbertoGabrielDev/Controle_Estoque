<template>
  <div>
    <div date-rangepicker class="flex items-center">
      <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
          <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
          </svg>
        </div>
        <input name="start" type="date" v-model="startDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date start">
      </div>
      <span class="mx-4 text-gray-500">to</span>
      <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
          <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
          </svg>
        </div>
        <input name="end" type="date" v-model="endDate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date end">
      </div>
    </div>

    <button @click="fetchData" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Fetch Data</button>

    <div class="chart-container">
      <component :is="chartType" :data="data" :options="options" />
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
  LinearScale
} from 'chart.js'
import { Bar, Line, Doughnut } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement,ArcElement,LineElement, Title, Tooltip, Legend)

export default {
  name: 'App',
  components: {
    Bar,
    Line,
    Doughnut
  },
  data() {
    return {
      startDate: '', 
      endDate: '',
      chartType: 'Doughnut', // Defina o tipo de gráfico padrão
      data: {
        labels: [],
        datasets: [{
          data: [],
          backgroundColor: [] // Array para cores
        }]
      },
      options: {
        responsive: true
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
  mounted() {
    this.fetchData();
   
  },
  methods: {
      fetchData(){
      let url = 'http://127.0.0.1:8000/verdurao/estoque/grafico-filtro'; //aqui ele me retorna todos os dados caso não tenha mes definido padrão

      if (this.startDate && this.endDate) {
        url += `?start_date=${this.startDate}&end_date=${this.endDate}`;
      }

      fetch(url)
      .then((response) => response.json())
      .then((fetchedData) => {
        this.data = {
       //   ...this.data, // Spread existing data properties
          labels: fetchedData.labels,
          datasets: [{ data: fetchedData.values,
            backgroundColor : this.generateRandomColors(fetchedData.labels.length)
           }],
          
        };
      });
      },

    // Função para gerar cores aleatórias
    generateRandomColors(count) {
      const colors = [];
      for (let i = 0; i < count; i++) {
        // Gerar uma cor aleatória no formato hexadecimal
        const color = '#' + Math.floor(Math.random() * 16777215).toString(16);
        colors.push(color);
      }
      return colors;
    }
  }
}
</script>

<style>
.chart-container {
  width: 800px;
  height: 600px;
}
</style>