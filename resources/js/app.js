
window.Vue = require('vue');

Vue.component('grafico' , require('./vuejs/Grafico.vue').default);

const app = new Vue({
    el: '#app'
});