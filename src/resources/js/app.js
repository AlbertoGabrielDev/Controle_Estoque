import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import SidebarLayout from './Layouts/Sidebar.vue'          // layout do módulo WPP (Vue)
import PrincipalLayout from './Layouts/PrincipalLayout.vue'// novo layout que criamos acima

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

// Páginas que usam o sidebar em Vue (WhatsApp)
const USE_VUE_SIDEBAR = [
  /^Wpp\//,
  /^Bot\//,
]

// Páginas que devem usar o "principal" (antigo Blade) em Vue
const USE_PRINCIPAL = [
  /^Dashboard\//,
  /^Vendas\//,
  /^Estoque\//,
  /^Categoria\//,
  /^Fornecedor\//,
  /^Marca\//,
  /^Usuario\//,
  /^Unidade\//,
  /^Roles\//,
]

createInertiaApp({
  title: (title) => `${title} - ${appName}`,

  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue')
    return resolvePageComponent(`./Pages/${name}.vue`, pages).then((mod) => {
      const page = mod.default || mod

      const isWpp   = USE_VUE_SIDEBAR.some(rx => rx.test(name))
      const isDash  = USE_PRINCIPAL.some(rx => rx.test(name))

      if (isWpp) {
        page.layout = page.layout || SidebarLayout
      } else if (isDash) {
        page.layout = page.layout || PrincipalLayout
      } else {
      }

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
