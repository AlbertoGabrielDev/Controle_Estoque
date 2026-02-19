import './bootstrap'
import '../css/app.css'
import '@fortawesome/fontawesome-free/css/fontawesome.min.css';
import '@fortawesome/fontawesome-free/css/solid.min.css'

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'

// ✅ Toasts (Vue 3)
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'


import SidebarLayout from './Layouts/Sidebar.vue'          // layout do módulo WPP (Vue)
import PrincipalLayout from './Layouts/PrincipalLayout.vue'// novo layout que criamos acima

import { initializeTheme } from './composables/useTheme'

import $ from 'jquery';
window.$ = window.jQuery = $;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

initializeTheme()

// Regras de layout
const USE_VUE_SIDEBAR = [/^Wpp\//, /^Bot\//]
const USE_PRINCIPAL   = [
  /^Dashboard\//, /^Calendar\//, /^Vendas\//, /^Sales\//, /^Spreadsheets\//, /^Estoque\//, /^Categoria\//,
  /^Fornecedor\//, /^Marca\//, /^Usuario\//, /^Unidade\//, /^Roles\//,
  /^Brands\//, /^Units\//, /^Categories\//, /^Suppliers\//, /^Users\//,
  /^Products\//, /^Stock\//,
  /^Clients\//, /^Segments\//, /^Taxes\//,
]

// Navegação Inertia por prefixo de rota
const INERTIA_PREFIXES = [
  'wpp.', 'bot.', 'taxes.',
  'categoria.', 'categorias.',
  'clientes.', 'segmentos.',
  'produtos.', 'estoque.',
  'marca.', 'unidade.', 'unidades.',
  'fornecedor.', 'usuario.', 'roles.',
  'vendas.', 'dashboard.',
  'spreadsheet.', 'calendar.',
]
const isInertiaMenu = (item) => {
  if (typeof item?.inertia === 'boolean') return item.inertia
  const name = item?.route || ''
  return !!name && INERTIA_PREFIXES.some(p => name.startsWith(p))
}
const tagFor = (item) => (isInertiaMenu(item) ? Link : 'a')
const linkClass = (routeName) => {
  let active = false
  try { active = route().current(routeName) } catch (_) { active = false }
  return [
    'flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100',
    active ? 'bg-cyan-50 text-cyan-600' : ''
  ].join(' ')
}

createInertiaApp({
  title: (title) => `${title} - ${appName}`,

  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue')
    return resolvePageComponent(`./Pages/${name}.vue`, pages).then((mod) => {
      const page = mod.default || mod
      const isWpp = USE_VUE_SIDEBAR.some(rx => rx.test(name))
      const isDash = USE_PRINCIPAL.some(rx => rx.test(name))

      if (isWpp)       page.layout = page.layout || SidebarLayout
      else if (isDash) page.layout = page.layout || PrincipalLayout

      return mod
    })
  },

  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      // ✅ registra o plugin de Toast
      .use(Toast, {
        position: 'top-right',
        timeout: 3000,
        closeOnClick: true,
        pauseOnFocusLoss: true,
        pauseOnHover: true,
        draggable: true,
        draggablePercent: 0.2,
        showCloseButtonOnHover: false,
        hideProgressBar: false,
        closeButton: 'button',
        icon: true,
        rtl: false,
      })

    // ✅ deixa um helper global de toast (para usar fora de componentes)
    window.showToast = (message, type = 'success') => {
      const t = app.config.globalProperties.$toast
      if (!t) return console.warn('Toast não disponível:', message)
      switch ((type || 'success').toLowerCase()) {
        case 'error':   t.error(message); break
        case 'info':    t.info(message); break
        case 'warning': t.warning(message); break
        default:        t.success(message); break
      }
    }

    app.mount(el)
    return app
  },

  progress: { color: '#4B5563' },
})

// ===== util =====
function getCsrf() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta?.content || '';
}

// ===== handler global para .toggle-status (agora usando showToast global) =====
document.addEventListener('click', async (ev) => {
  const btn = ev.target.closest('.toggle-status');
  if (!btn) return;

  ev.preventDefault();
  if (btn.dataset.processing === '1') return;
  btn.dataset.processing = '1';

  try {
    const res = await fetch(btn.dataset.url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf(),
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({})
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();

    const active = Number(data.new_status) === 1;
    btn.dataset.active = active ? '1' : '0';
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');

    btn.classList.toggle('bg-green-500', active);
    btn.classList.toggle('hover:bg-green-600', active);
    btn.classList.toggle('bg-red-400', !active);
    btn.classList.toggle('hover:bg-red-500', !active);

    const message = active ? 'Status ativado com sucesso!' : 'Status desativado com sucesso!';
    window.showToast(message, active ? 'success' : 'warning');

  } catch (err) {
    console.error('toggle-status failed', err);
    window.showToast(err?.message || 'Erro ao atualizar status.', 'error');
  } finally {
    btn.dataset.processing = '0';
  }
});
