import './bootstrap'
import '../css/app.css'
import '@fortawesome/fontawesome-free/css/fontawesome.min.css';
import '@fortawesome/fontawesome-free/css/solid.min.css'

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

import { ZiggyVue } from 'ziggy-js'

import SidebarLayout from './Layouts/Sidebar.vue'          // layout do módulo WPP (Vue)
import PrincipalLayout from './Layouts/PrincipalLayout.vue'// novo layout que criamos acima

import $ from 'jquery';
window.$ = window.jQuery = $;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

// Páginas que usam o sidebar em Vue (WhatsApp)
const USE_VUE_SIDEBAR = [
  /^Wpp\//,
  /^Bot\//,
]

// Páginas que devem usar o "principal" (antigo Blade) em Vue
const USE_PRINCIPAL = [
  /^Dashboard\//,
  /^Calendar\//,
  /^Vendas\//,
  /^Estoque\//,
  /^Categoria\//,
  /^Fornecedor\//,
  /^Marca\//,
  /^Usuario\//,
  /^Unidade\//,
  /^Roles\//,
  /^Clients\//,
  /^Segments\//,
  /^Taxes\//, // Módulo de Imposto (Taxas)
]

// IMPORTANTe: rotas tratadas como Inertia Link (sem full reload)
// além de 'wpp.' e 'bot.', incluí 'taxes.' para o módulo de imposto
const INERTIA_PREFIXES = ['wpp.', 'bot.', 'taxes.']

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

      if (isWpp) {
        page.layout = page.layout || SidebarLayout
      } else if (isDash) {
        page.layout = page.layout || PrincipalLayout
      } else {
        // página sem layout específico → usa o próprio
      }

      return mod
    })
  },

  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      // ✅ Registra o plugin do Ziggy passando o objeto de rotas
      .use(ZiggyVue, Ziggy)
      .mount(el)
  },

  progress: { color: '#4B5563' },
})

function getCsrf() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta?.content || '';
}

// Handler para botões .toggle-status (mantido e com fix no catch)
document.addEventListener('click', async (ev) => {
  const btn = ev.target.closest('.toggle-status');
  if (!btn) return;

  ev.preventDefault();
  if (btn.dataset.processing === '1') return;
  btn.dataset.processing = '1';

  try {
    const res = await fetch(btn.dataset.url, {
      method: 'POST',
      credentials: 'same-origin',              // manda os cookies
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

    // Atualiza UI
    const active = Number(data.new_status) === 1;
    btn.dataset.active = active ? '1' : '0';
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');

    btn.classList.toggle('bg-green-500', active);
    btn.classList.toggle('hover:bg-green-600', active);
    btn.classList.toggle('bg-red-400', !active);
    btn.classList.toggle('hover:bg-red-500', !active);

    const message = active ? 'Status ativado com sucesso!' : 'Status desativado com sucesso!';
    showToast(message, active ? 'success' : 'error');

  } catch (err) {
    console.error('toggle-status failed', err);
    // FIX: 'data' não existe no catch; usar a mensagem do erro
    showToast(err?.message || 'Erro ao atualizar status.', 'error');
  } finally {
    btn.dataset.processing = '0';
  }
});

function showToast(message, type = 'success') {
  const container = document.getElementById('toast-container') || (() => {
    const d = document.createElement('div');
    d.id = 'toast-container';
    d.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
    document.body.appendChild(d);
    return d;
  })();

  const toast = document.createElement('div');
  toast.className = `toast flex items-center w-full max-w-xs p-4 rounded-lg shadow-sm text-sm fade-in gap-3
    ${type === 'success' ? 'bg-green-400 text-white' : ''}
    ${type === 'error' ? 'bg-red-400 text-white' : ''}`;

  toast.innerHTML = `<span class="flex-1">${message}</span>
    <button type="button" class="ml-2 text-white/80 hover:text-white" onclick="this.closest('.toast').remove()">✕</button>`;

  container.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('fade-out');
    setTimeout(() => toast.remove(), 500);
  }, 3000);
}
