<template>
  <Bar :data="data" :options="options" />
</template>

<script lang="ts">
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

export default {
  name: 'App',
  components: {
    Bar
  },
  data() {
    return {
      data: {
        labels: [],
        datasets: []
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
        ...this.data, // Spread existing data properties
        labels: fetchedData.labels,
        datasets: [{ data: fetchedData.values }],
      };
    });
},
  // computed : {
  //   labels : function () {
  //     this.labels;
  //   }
  // },
}
</script>