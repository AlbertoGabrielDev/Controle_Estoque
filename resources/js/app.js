import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue/dist/vue.esm-bundler';

// import {createApp} from 'vue'
import Home from './components/Home.vue';

createApp({
    components: {
        Home,
    }
}).mount('#app');