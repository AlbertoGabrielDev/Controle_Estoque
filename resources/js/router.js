import Vue from 'vue';
import VueRouter from 'vue-router';
// import Index from '../components/usuario/Index.vue'
import Chart from './components/OptionsCharts/Chart.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/index',
    name: 'Chart',
    component: Chart
  }
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
});

export default router;
