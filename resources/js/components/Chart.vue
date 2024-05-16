<template>
  <div>
    <component :is="chartType" :data="data" :options="options" />
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
  mounted() {
    fetch('http://127.0.0.1:8000/verdurao/estoque/teste')
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
  methods: {
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
