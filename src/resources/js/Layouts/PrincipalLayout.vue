<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ThemeToggle from '@/components/ThemeToggle.vue'

const page = usePage()

const user = computed(() => page.props.auth?.user ?? null)
const menus = computed(() => page.props.menus ?? [])
const displayName = computed(() => String(user.value?.name ?? 'Usuario'))
const firstName = computed(() => displayName.value.split(' ')[0] || 'Usuario')

const sidebarOpen = ref(false)
const sidebarCollapsed = ref(false)
const isDesktopViewport = ref(false)
const openGroups = reactive({})
let resizeHandler = null
const SIDEBAR_COLLAPSE_KEY = 'controle-estoque:sidebar-collapsed'

const isDesktop = () => window.innerWidth >= 768
const compactSidebar = computed(() => isDesktopViewport.value && sidebarCollapsed.value)

const hasChildren = (item) => Array.isArray(item?.children) && item.children.length > 0

const routeIsActive = (routeName) => {
    if (!routeName) return false

    try {
        return route().current(routeName)
    } catch (_) {
        return false
    }
}

const resolveHref = (item) => {
    if (item?.href) return item.href
    if (!item?.route) return null

    try {
        return route(item.route)
    } catch (_) {
        return null
    }
}

const hasRoute = (item) => !!resolveHref(item)

const readSidebarPreference = () => {
    if (typeof window === 'undefined') return false

    try {
        return window.localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === '1'
    } catch (_) {
        return false
    }
}

const writeSidebarPreference = (value) => {
    if (typeof window === 'undefined') return

    try {
        window.localStorage.setItem(SIDEBAR_COLLAPSE_KEY, value ? '1' : '0')
    } catch (_) {
        // Ignora erro de armazenamento (modo privado ou bloqueio de localStorage).
    }
}

const isMenuActive = (item) => {
    if (routeIsActive(item?.route)) return true
    if (!hasChildren(item)) return false

    return item.children.some((child) => routeIsActive(child?.route))
}

const iconClass = (item) => {
    const raw = String(item?.icon ?? '')
        .replace(/\bmr-\d+\b/g, '')
        .trim()

    return [raw || 'fas fa-circle', 'shrink-0 w-4 text-center text-sm'].join(' ')
}

const menuItemClass = (item, child = false) => {
    const active = isMenuActive(item)

    if (child) {
        return [
            'group flex w-full items-center gap-2 rounded-xl px-3 py-2 text-[13px] font-medium transition-colors duration-150',
            active
                ? 'bg-cyan-50 text-cyan-700 shadow-sm shadow-cyan-100 dark:bg-cyan-500/15 dark:text-cyan-100 dark:shadow-none'
                : 'text-slate-600 hover:bg-slate-100/80 dark:text-slate-300 dark:hover:bg-slate-800/70',
        ].join(' ')
    }

    return [
        'group flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
        active
            ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20 dark:bg-cyan-500/20 dark:text-cyan-100 dark:shadow-none'
            : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800/80',
    ].join(' ')
}

const syncOpenGroupsWithRoute = () => {
    menus.value.forEach((menu) => {
        if (!hasChildren(menu)) return

        const shouldOpen = menu.children.some((child) => routeIsActive(child?.route))
        if (shouldOpen) {
            openGroups[menu.id] = true
            return
        }

        if (typeof openGroups[menu.id] !== 'boolean') {
            openGroups[menu.id] = false
        }
    })
}

const toggleGroup = (menuId) => {
    if (compactSidebar.value) {
        sidebarCollapsed.value = false
        writeSidebarPreference(false)
        openGroups[menuId] = true
        return
    }

    openGroups[menuId] = !openGroups[menuId]
}

const toggleSidebarCollapse = () => {
    if (!isDesktopViewport.value) return

    sidebarCollapsed.value = !sidebarCollapsed.value
    writeSidebarPreference(sidebarCollapsed.value)

    if (sidebarCollapsed.value) {
        Object.keys(openGroups).forEach((menuId) => {
            openGroups[menuId] = false
        })
    }
}

const closeMobileSidebar = () => {
    if (!isDesktop()) {
        sidebarOpen.value = false
    }
}

onMounted(() => {
    isDesktopViewport.value = isDesktop()
    sidebarOpen.value = isDesktopViewport.value
    sidebarCollapsed.value = isDesktopViewport.value ? readSidebarPreference() : false
    syncOpenGroupsWithRoute()

    resizeHandler = () => {
        isDesktopViewport.value = isDesktop()
        sidebarOpen.value = isDesktopViewport.value

        if (!isDesktopViewport.value) {
            sidebarCollapsed.value = false
            return
        }

        sidebarCollapsed.value = readSidebarPreference()
    }

    window.addEventListener('resize', resizeHandler)
})

onBeforeUnmount(() => {
    if (resizeHandler) {
        window.removeEventListener('resize', resizeHandler)
    }
})

watch(
    () => page.url,
    () => {
        syncOpenGroupsWithRoute()
        closeMobileSidebar()
    }
)

watch(
    menus,
    () => {
        syncOpenGroupsWithRoute()
    },
    { immediate: true }
)
</script>

<template>
    <div class="relative min-h-screen overflow-hidden bg-slate-100 text-slate-900 transition-colors dark:bg-slate-950 dark:text-slate-100">
        <div class="erp-grid-bg pointer-events-none absolute inset-0"></div>
        <div class="pointer-events-none absolute -right-20 -top-24 h-72 w-72 rounded-full bg-cyan-300/30 blur-3xl dark:bg-cyan-500/20"></div>
        <div class="pointer-events-none absolute -bottom-28 -left-16 h-72 w-72 rounded-full bg-emerald-300/25 blur-3xl dark:bg-emerald-500/15"></div>

        <div class="relative flex min-h-screen flex-col">
            <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80">
                <div class="mx-auto flex h-16 max-w-[1600px] items-center gap-3 px-3 md:h-[72px] md:gap-4 md:px-5">
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition-colors hover:bg-slate-50 md:hidden dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                        @click="sidebarOpen = !sidebarOpen"
                    >
                        <span class="sr-only">Abrir menu</span>
                        <svg v-if="!sidebarOpen" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-600 text-white shadow-lg shadow-cyan-600/30 dark:bg-cyan-500 dark:shadow-cyan-500/20">
                            <i class="fas fa-boxes-stacked text-sm"></i>
                        </div>

                        <div class="min-w-0">
                            <p class="truncate text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-100">Controle Estoque</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">ERP de operacoes e inventario</p>
                        </div>
                    </div>

                    <div class="hidden min-w-0 flex-1 md:block">
                        <div class="rounded-xl border border-slate-200/80 bg-slate-50/80 px-2 py-1 dark:border-slate-700 dark:bg-slate-800/40">
                            <slot name="search" />
                        </div>
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        <div
                            v-if="user"
                            class="hidden items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600 lg:flex dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        >
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span class="max-w-[140px] truncate">{{ firstName }}</span>
                        </div>

                        <ThemeToggle />
                        <slot name="top-right" />
                    </div>
                </div>
            </header>

            <div class="relative flex flex-1 gap-3 p-3 md:gap-4 md:p-4">
                <div
                    v-if="sidebarOpen"
                    class="absolute inset-0 z-20 bg-slate-950/40 md:hidden"
                    @click="sidebarOpen = false"
                />

                <aside
                    class="absolute inset-y-0 left-0 z-30 w-72 transition-transform duration-300 ease-out md:relative md:inset-auto md:translate-x-0"
                    :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', compactSidebar ? 'md:w-20' : 'md:w-72']"
                >
                    <div class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200/70 bg-white/90 shadow-xl shadow-slate-200/60 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90 dark:shadow-none">
                        <div class="border-b border-slate-200/80 px-3 py-4 dark:border-slate-800">
                            <div class="flex items-center" :class="compactSidebar ? 'flex-col justify-center gap-2' : 'justify-between'">
                                <div v-if="compactSidebar" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-white dark:bg-slate-800">
                                    <i class="fas fa-cubes text-sm"></i>
                                </div>

                                <div v-else>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Menu principal</p>
                                    <p v-if="user" class="mt-1 text-sm font-medium text-slate-700 dark:text-slate-200">Ola, {{ displayName }}</p>
                                </div>

                                <button
                                    type="button"
                                    class="hidden h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700 md:inline-flex dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                    :title="compactSidebar ? 'Expandir menu lateral' : 'Colapsar menu lateral'"
                                    @click="toggleSidebarCollapse"
                                >
                                    <i :class="compactSidebar ? 'fas fa-angle-right text-sm' : 'fas fa-angle-left text-sm'"></i>
                                </button>
                            </div>
                        </div>

                        <nav class="erp-nav-scroll flex-1 space-y-1 overflow-y-auto p-3" :class="compactSidebar ? 'px-2' : 'px-3'">
                            <template v-if="menus.length">
                                <template v-for="menu in menus" :key="menu.id">
                                    <div v-if="hasChildren(menu)" class="space-y-1">
                                        <button
                                            type="button"
                                            :class="[menuItemClass(menu), compactSidebar ? 'justify-center px-2' : '']"
                                            :title="compactSidebar ? menu.name : undefined"
                                            @click="toggleGroup(menu.id)"
                                        >
                                            <i :class="iconClass(menu)"></i>
                                            <span v-if="!compactSidebar" class="truncate">{{ menu.name }}</span>
                                            <i
                                                v-if="!compactSidebar"
                                                class="fas fa-chevron-down ml-auto text-[11px] text-slate-400 transition-transform duration-200"
                                                :class="{ 'rotate-180': openGroups[menu.id] }"
                                            ></i>
                                        </button>

                                        <transition name="submenu">
                                            <div v-if="!compactSidebar && openGroups[menu.id]" class="ml-3 space-y-1 border-l border-slate-200/90 pl-3 dark:border-slate-700">
                                                <template v-for="child in menu.children" :key="child.id">
                                                    <Link
                                                        v-if="hasRoute(child)"
                                                        :href="resolveHref(child)"
                                                        :class="menuItemClass(child, true)"
                                                        @click="closeMobileSidebar"
                                                    >
                                                        <i :class="iconClass(child)"></i>
                                                        <span class="truncate">{{ child.name }}</span>
                                                    </Link>
                                                </template>
                                            </div>
                                        </transition>
                                    </div>

                                    <Link
                                        v-else-if="hasRoute(menu)"
                                        :href="resolveHref(menu)"
                                        :class="[menuItemClass(menu), compactSidebar ? 'justify-center px-2' : '']"
                                        :title="compactSidebar ? menu.name : undefined"
                                        @click="closeMobileSidebar"
                                    >
                                        <i :class="iconClass(menu)"></i>
                                        <span v-if="!compactSidebar" class="truncate">{{ menu.name }}</span>
                                    </Link>
                                </template>
                            </template>

                            <div v-else class="rounded-xl border border-dashed border-slate-300 px-3 py-4 text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                Nenhum menu disponivel para este usuario.
                            </div>
                        </nav>

                        <form class="border-t border-slate-200/80 p-3 dark:border-slate-800" @submit.prevent="$inertia.post('/logout')">
                            <button
                                type="submit"
                                class="flex w-full items-center rounded-xl py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-rose-50 hover:text-rose-700 dark:text-slate-200 dark:hover:bg-rose-500/10 dark:hover:text-rose-200"
                                :class="compactSidebar ? 'justify-center px-2' : 'gap-2 px-3'"
                                :title="compactSidebar ? 'Sair da sessao' : undefined"
                            >
                                <i class="fas fa-sign-out-alt shrink-0 w-4 text-center text-sm"></i>
                                <span v-if="!compactSidebar" class="truncate">Sair da sessao</span>
                            </button>
                        </form>
                    </div>
                </aside>

                <main class="min-w-0 flex-1 overflow-hidden">
                    <section class="erp-workspace h-full overflow-auto rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-xl shadow-slate-200/50 backdrop-blur transition-colors dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-none md:p-6">
                        <slot />
                    </section>
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
}

.erp-grid-bg {
    background-image:
        radial-gradient(circle at 10% 20%, rgba(14, 165, 233, 0.14), transparent 34%),
        radial-gradient(circle at 85% 90%, rgba(16, 185, 129, 0.11), transparent 32%),
        linear-gradient(to right, rgba(148, 163, 184, 0.09) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(148, 163, 184, 0.09) 1px, transparent 1px);
    background-size: auto, auto, 26px 26px, 26px 26px;
}

.erp-nav-scroll {
    scrollbar-width: thin;
    scrollbar-color: rgba(148, 163, 184, 0.7) transparent;
}

.erp-nav-scroll::-webkit-scrollbar {
    width: 8px;
}

.erp-nav-scroll::-webkit-scrollbar-thumb {
    border-radius: 9999px;
    background: rgba(148, 163, 184, 0.7);
}

.erp-nav-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.submenu-enter-active,
.submenu-leave-active {
    transition: all 0.2s ease;
}

.submenu-enter-from,
.submenu-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

:deep(.erp-workspace .bg-white.rounded),
:deep(.erp-workspace .bg-white.rounded-md),
:deep(.erp-workspace .bg-white.rounded-lg),
:deep(.erp-workspace .bg-white.rounded-xl),
:deep(.erp-workspace .bg-white.rounded-2xl),
:deep(.erp-workspace .bg-gray-50.rounded),
:deep(.erp-workspace .bg-gray-50.rounded-md),
:deep(.erp-workspace .bg-gray-50.rounded-lg),
:deep(.erp-workspace .bg-gray-50.rounded-xl),
:deep(.erp-workspace .bg-gray-50.rounded-2xl) {
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    background-color: rgba(255, 255, 255, 0.92);
    box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
}

:deep(.dark .erp-workspace .bg-white.rounded),
:deep(.dark .erp-workspace .bg-white.rounded-md),
:deep(.dark .erp-workspace .bg-white.rounded-lg),
:deep(.dark .erp-workspace .bg-white.rounded-xl),
:deep(.dark .erp-workspace .bg-white.rounded-2xl),
:deep(.dark .erp-workspace .bg-gray-50.rounded),
:deep(.dark .erp-workspace .bg-gray-50.rounded-md),
:deep(.dark .erp-workspace .bg-gray-50.rounded-lg),
:deep(.dark .erp-workspace .bg-gray-50.rounded-xl),
:deep(.dark .erp-workspace .bg-gray-50.rounded-2xl) {
    border-color: #334155;
    background-color: rgba(15, 23, 42, 0.82);
    box-shadow: none;
}

:deep(.erp-workspace label) {
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    color: #334155;
}

:deep(.dark .erp-workspace label) {
    color: #cbd5e1;
}

:deep(.erp-workspace input:not([type='checkbox']):not([type='radio']):not([type='file']):not([type='range'])),
:deep(.erp-workspace select),
:deep(.erp-workspace textarea) {
    min-height: 2.65rem;
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 0.75rem;
    background-color: rgba(255, 255, 255, 0.95);
    color: #0f172a;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
    transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
}

:deep(.erp-workspace textarea) {
    min-height: 6rem;
}

:deep(.erp-workspace input:not([type='checkbox']):not([type='radio']):not([type='file']):not([type='range']):focus),
:deep(.erp-workspace select:focus),
:deep(.erp-workspace textarea:focus) {
    outline: none;
    border-color: #06b6d4;
    box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
}

:deep(.dark .erp-workspace input:not([type='checkbox']):not([type='radio']):not([type='file']):not([type='range'])),
:deep(.dark .erp-workspace select),
:deep(.dark .erp-workspace textarea) {
    border-color: #475569;
    background-color: rgba(15, 23, 42, 0.9);
    color: #e2e8f0;
    box-shadow: none;
}

:deep(.dark .erp-workspace input::placeholder),
:deep(.dark .erp-workspace textarea::placeholder) {
    color: #94a3b8;
}

:deep(.erp-workspace button) {
    transition: transform 0.14s ease, box-shadow 0.14s ease, background-color 0.14s ease;
}

:deep(.erp-workspace button:hover) {
    transform: translateY(-1px);
}

:deep(.erp-workspace table) {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

:deep(.erp-workspace table thead th) {
    border-bottom: 1px solid #dbe4ee;
    background-color: #f8fafc;
    color: #475569;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

:deep(.erp-workspace table tbody td) {
    border-bottom: 1px solid #eef2f7;
    color: #334155;
}

:deep(.erp-workspace table tbody tr:hover td) {
    background-color: #f8fafc;
}

:deep(.dark .erp-workspace table thead th) {
    border-bottom-color: #334155;
    background-color: #0f172a;
    color: #94a3b8;
}

:deep(.dark .erp-workspace table tbody td) {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}

:deep(.dark .erp-workspace table tbody tr:hover td) {
    background-color: #111827;
}

:deep(.erp-workspace .dt-tailwind) {
    border: 1px solid #dbe4ee;
    border-radius: 1rem;
    background-color: rgba(255, 255, 255, 0.88);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}

:deep(.erp-workspace .dataTables_wrapper .dataTables_length select),
:deep(.erp-workspace .dataTables_wrapper .dataTables_filter input),
:deep(.erp-workspace .dataTables_wrapper .dataTables_paginate .paginate_button) {
    border-radius: 0.7rem !important;
    border: 1px solid #cbd5e1 !important;
    background-color: #ffffff !important;
    color: #334155 !important;
}

:deep(.erp-workspace .dataTables_wrapper .dataTables_paginate .paginate_button.current) {
    border-color: #0891b2 !important;
    background-color: #0891b2 !important;
    color: #ffffff !important;
}

:deep(.dark .erp-workspace .dt-tailwind) {
    border-color: #334155;
    background-color: rgba(15, 23, 42, 0.72);
    box-shadow: none;
}

:deep(.dark .erp-workspace .dataTables_wrapper .dataTables_length select),
:deep(.dark .erp-workspace .dataTables_wrapper .dataTables_filter input),
:deep(.dark .erp-workspace .dataTables_wrapper .dataTables_paginate .paginate_button) {
    border-color: #475569 !important;
    background-color: #0b1220 !important;
    color: #e2e8f0 !important;
}
</style>
