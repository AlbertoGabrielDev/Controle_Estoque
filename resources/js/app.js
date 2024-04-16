import Grafico from '../views/vuejs/Grafico.vue';
import { createApp } from 'vue';

const app = createApp({});
app.component('Grafico', Grafico);

app.mount('#app');

require('./bootstrap');