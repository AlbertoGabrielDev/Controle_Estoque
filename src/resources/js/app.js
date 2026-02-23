import './bootstrap'
import '../css/app.css'
import '@fortawesome/fontawesome-free/css/fontawesome.min.css'
import '@fortawesome/fontawesome-free/css/solid.min.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'

// Toasts (Vue 3)
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import SidebarLayout from './Layouts/Sidebar.vue'
import PrincipalLayout from './Layouts/PrincipalLayout.vue'

import { initializeTheme } from './composables/useTheme'

import $ from 'jquery'
window.$ = window.jQuery = $

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

initializeTheme()

const ERP_TOAST_CONTAINER_ID = 'erp-toast-container'

function ensureFallbackToastContainer() {
  if (typeof document === 'undefined') return null

  let container = document.getElementById(ERP_TOAST_CONTAINER_ID)
  if (!container) {
    container = document.createElement('div')
    container.id = ERP_TOAST_CONTAINER_ID
    container.className = 'erp-toast-container fixed right-4 flex flex-col gap-2'
    document.body.appendChild(container)
  }

  container.style.zIndex = '12000'
  container.style.top = 'var(--erp-toast-top-offset, 84px)'
  container.style.maxWidth = 'calc(100vw - 2rem)'
  return container
}

function fallbackToast(message, type = 'success') {
  if (typeof document === 'undefined') return

  const container = ensureFallbackToastContainer()
  if (!container) return

  const palette = {
    success: 'bg-green-500 text-white',
    warning: 'bg-amber-500 text-white',
    error: 'bg-rose-500 text-white',
    info: 'bg-sky-500 text-white',
  }

  const level = String(type || 'success').toLowerCase()
  const tone = palette[level] || palette.success

  const toast = document.createElement('div')
  toast.className = `toast pointer-events-auto flex items-center w-full max-w-sm gap-3 rounded-xl px-4 py-3 text-sm shadow-lg ${tone}`

  const text = document.createElement('span')
  text.className = 'flex-1'
  text.textContent = String(message ?? '')

  const closeButton = document.createElement('button')
  closeButton.type = 'button'
  closeButton.className = 'opacity-80 hover:opacity-100'
  closeButton.setAttribute('aria-label', 'Fechar')
  closeButton.textContent = 'x'
  closeButton.addEventListener('click', () => toast.remove())

  toast.appendChild(text)
  toast.appendChild(closeButton)

  container.appendChild(toast)
  toast.style.opacity = '0'

  requestAnimationFrame(() => {
    toast.style.transition = 'opacity .2s ease'
    toast.style.opacity = '1'
  })

  setTimeout(() => {
    toast.style.transition = 'opacity .35s ease'
    toast.style.opacity = '0'
    setTimeout(() => toast.remove(), 350)
  }, 2800)
}

function safeShowToast(message, type = 'success') {
  if (typeof window === 'undefined') return

  if (typeof window.showToast === 'function') {
    try {
      window.showToast(message, type)
      return
    } catch (_) {
      // fallback below
    }
  }

  fallbackToast(message, type)
}

if (typeof window !== 'undefined' && typeof window.showToast !== 'function') {
  window.showToast = fallbackToast
}

// Regras de layout
const USE_VUE_SIDEBAR = [/^Wpp\//, /^Bot\//]
const USE_PRINCIPAL = [
  /^Dashboard\//, /^Calendar\//, /^Vendas\//, /^Sales\//, /^Spreadsheets\//, /^Estoque\//, /^Categoria\//,
  /^Fornecedor\//, /^Marca\//, /^Usuario\//, /^Unidade\//, /^Roles\//,
  /^Brands\//, /^Units\//, /^Categories\//, /^Suppliers\//, /^Users\//,
  /^Products\//, /^Stock\//,
  /^Clients\//, /^Segments\//, /^Taxes\//,
  /^Settings\//,
  // Cadastro Mestre (MDM)
  /^MeasureUnits\//, /^Items\//, /^PriceTables\//, /^CostCenters\//, /^AccountingAccounts\//,
]

createInertiaApp({
  title: (title) => `${title} - ${appName}`,

  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue')
    return resolvePageComponent(`./Pages/${name}.vue`, pages).then((mod) => {
      const page = mod.default || mod
      const isWpp = USE_VUE_SIDEBAR.some((rx) => rx.test(name))
      const isDash = USE_PRINCIPAL.some((rx) => rx.test(name))

      if (isWpp) page.layout = page.layout || SidebarLayout
      else if (isDash) page.layout = page.layout || PrincipalLayout

      return mod
    })
  },

  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
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

    window.showToast = (message, type = 'success') => {
      const t = app.config.globalProperties.$toast
      if (!t) {
        fallbackToast(message, type)
        return
      }

      switch ((type || 'success').toLowerCase()) {
        case 'error':
          t.error(message)
          break
        case 'info':
          t.info(message)
          break
        case 'warning':
          t.warning(message)
          break
        default:
          t.success(message)
          break
      }
    }

    app.mount(el)
    return app
  },

  progress: { color: '#4B5563' },
})

function getCsrf() {
  const meta = document.querySelector('meta[name="csrf-token"]')
  return meta?.content || ''
}

document.addEventListener('click', async (ev) => {
  const btn = ev.target.closest('.toggle-status')
  if (!btn) return

  const url = btn.dataset.url
  if (!url) return

  ev.preventDefault()
  if (btn.dataset.processing === '1') return
  btn.dataset.processing = '1'

  try {
    const res = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf(),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({}),
    })

    if (!res.ok) throw new Error('HTTP ' + res.status)

    const data = await res.json()
    const active = Number(data.new_status) === 1

    btn.dataset.active = active ? '1' : '0'
    btn.setAttribute('aria-pressed', active ? 'true' : 'false')

    btn.classList.toggle('bg-green-500', active)
    btn.classList.toggle('hover:bg-green-600', active)
    btn.classList.toggle('bg-red-400', !active)
    btn.classList.toggle('hover:bg-red-500', !active)

    const message = data?.message || (active
      ? 'Status ativado com sucesso!'
      : 'Status desativado com sucesso!')

    const type = data?.type || (active ? 'success' : 'warning')
    safeShowToast(message, type)
  } catch (err) {
    console.error('toggle-status failed', err)
    safeShowToast(err?.message || 'Erro ao atualizar status.', 'error')
  } finally {
    btn.dataset.processing = '0'
  }
})
