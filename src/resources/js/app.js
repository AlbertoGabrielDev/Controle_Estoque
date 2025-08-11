import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import SidebarLayout from './Layouts/Sidebar.vue'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,

  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue')
    return resolvePageComponent(`./Pages/${name}.vue`, pages).then((mod) => {
      const page = mod.default || mod
      // Layout padrão como COMPONENTE (não função!)
      page.layout = page.layout || SidebarLayout
      return mod
    })
  },

  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .mount(el)
  },

  progress: { color: '#4B5563' },
})
