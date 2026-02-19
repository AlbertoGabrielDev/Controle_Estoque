<script setup>
import { reactive, ref, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ThemeToggle from '@/components/ThemeToggle.vue'
const page = usePage()
const menus = page.props.menus ?? []
const user = page.props.auth?.user ?? null
const sidebarOpen = ref(false)
const open = reactive({})

const INERTIA_PREFIXES = [
    'wpp.', 'bot.', 'taxes.',
    'categoria.', 'categorias.',
    'clientes.', 'segmentos.',
    'produtos.', 'estoque.',
    'marca.', 'unidade.', 'unidades.',
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
        'flex items-center p-2 text-gray-600 rounded-lg transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800',
        active ? 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-200' : ''
    ].join(' ')
}

const toggleGroup = (id) => { open[id] = !open[id] }
const closeMobile = () => { if (window.innerWidth < 768) sidebarOpen.value = false }

onMounted(() => {
    if (window.innerWidth >= 768) sidebarOpen.value = true
    menus.forEach(m => {
        if (m.children?.some(c => {
            try { return route().current(c.route) } catch (_) { return false }
        })) open[m.id] = true
    })
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) sidebarOpen.value = true
    })
})
</script>

<template>
    <div class="flex h-screen flex-col bg-gray-50 text-gray-900 dark:bg-slate-950 dark:text-slate-100">
        <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>
        <nav class="bg-white shadow-sm dark:bg-slate-900 dark:shadow-slate-900/40">
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                <div class="relative flex h-16 items-center justify-between">
                    <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                        <button
                            class="ml-2 inline-flex items-center justify-center rounded-md p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-slate-800 dark:hover:text-white"
                            @click="sidebarOpen = !sidebarOpen">
                            <span class="sr-only">Abrir menu</span>
                            <svg v-if="!sidebarOpen" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                        <div class="flex shrink-0 items-center ml-6">
                            <img class="h-8 w-auto"
                                src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500" alt="Logo">
                        </div>
                        <div class="hidden sm:ml-6 sm:block">
                            <slot name="search" />
                        </div>
                    </div>
                    <div
                        class="absolute inset-y-0 right-0 flex items-center gap-2 pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                        <ThemeToggle />
                        <slot name="top-right" />
                    </div>
                </div>
            </div>
        </nav>
        <div class="flex flex-1 overflow-hidden">
            <aside class="absolute inset-y-0 left-0 z-20 h-full w-64 space-y-6 border-r border-gray-100 bg-white py-7 px-2 transition duration-200 ease-in-out dark:border-slate-800 dark:bg-slate-900 md:relative md:translate-x-0"
                :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full']">
                <nav>
                    <div v-if="user" class="mb-2 px-2 text-xs text-gray-400 dark:text-gray-400">
                        Olá, {{ user.name }}
                    </div>
                    <template v-for="menu in menus" :key="menu.id">
                        <div v-if="menu.children && menu.children.length" class="relative">
                            <button @click="toggleGroup(menu.id)"
                                class="flex w-full items-center rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800">
                                <i :class="[menu.icon, 'mr-2']"></i>
                                <span class="ml-1">{{ menu.name }}</span>
                                <i class="fas fa-chevron-down ml-auto text-xs transition-transform"
                                    :class="{ 'rotate-180': open[menu.id] }"></i>
                            </button>
                            <div v-show="open[menu.id]" class="ml-4">
                                <template v-for="child in menu.children" :key="child.id">
                                    <component :is="tagFor(child)" :href="route(child.route)"
                                        :class="linkClass(child.route)" @click="closeMobile">
                                        <i :class="[child.icon, 'mr-2']"></i>
                                        <span class="ml-1">{{ child.name }}</span>
                                    </component>
                                </template>
                            </div>
                        </div>

                        <div v-else>
                            <component :is="tagFor(menu)" :href="route(menu.route)" :class="linkClass(menu.route)"
                                @click="closeMobile">
                                <i :class="[menu.icon, 'mr-2']"></i>
                                <span class="ml-1">{{ menu.name }}</span>
                            </component>
                        </div>
                    </template>
                </nav>
                <form @submit.prevent="$inertia.post('/logout')" class="absolute bottom-0 left-0 w-full px-2">
                    <button type="submit"
                        class="flex w-full items-center rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        <span class="ml-1">Sair</span>
                    </button>
                </form>
            </aside>
            <main class="flex-1 overflow-auto bg-gray-50 p-4 transition-colors dark:bg-slate-950">
                <slot />
            </main>
        </div>
    </div>
</template>
<style scoped>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
