<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ThemeToggle from '@/components/ThemeToggle.vue'

const page = usePage()

const user = computed(() => page.props.auth?.user ?? null)
const menus = computed(() => page.props.menus ?? [])

const sidebarOpen = ref(false)
const openGroups = reactive({})
let resizeHandler = null

const isDesktop = () => window.innerWidth >= 768

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

const isMenuActive = (item) => {
    if (routeIsActive(item?.route)) return true
    if (!hasChildren(item)) return false

    return item.children.some((child) => routeIsActive(child?.route))
}

const itemClass = (item) => {
    const active = isMenuActive(item)

    return [
        'flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800',
        active ? 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-200' : '',
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
    openGroups[menuId] = !openGroups[menuId]
}

const closeMobileSidebar = () => {
    if (!isDesktop()) {
        sidebarOpen.value = false
    }
}

onMounted(() => {
    sidebarOpen.value = isDesktop()
    syncOpenGroupsWithRoute()

    resizeHandler = () => {
        sidebarOpen.value = isDesktop()
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
    }
)
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-50 text-gray-900 dark:bg-slate-950 dark:text-slate-100">
        <div id="toast-container" class="fixed right-4 top-4 z-50 flex flex-col gap-2"></div>

        <nav class="sticky top-0 z-40 border-b border-gray-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:shadow-slate-900/40">
            <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">
                <div class="relative flex h-16 items-center justify-between">
                    <div class="absolute inset-y-0 left-0 flex items-center md:hidden">
                        <button
                            class="ml-2 inline-flex items-center justify-center rounded-md p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-slate-800 dark:hover:text-white"
                            @click="sidebarOpen = !sidebarOpen"
                        >
                            <span class="sr-only">Abrir menu</span>
                            <svg v-if="!sidebarOpen" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-1 items-center justify-center md:justify-start">
                        <div class="ml-6 flex shrink-0 items-center md:ml-0">
                            <span class="text-lg font-semibold tracking-tight text-cyan-700 dark:text-cyan-300">Controle Estoque</span>
                        </div>

                        <div class="hidden md:ml-6 md:block">
                            <slot name="search" />
                        </div>
                    </div>

                    <div class="absolute inset-y-0 right-0 flex items-center gap-2 pr-2 md:static md:inset-auto md:ml-6 md:pr-0">
                        <ThemeToggle />
                        <slot name="top-right" />
                    </div>
                </div>
            </div>
        </nav>

        <div class="relative flex flex-1 overflow-hidden">
            <div
                v-if="sidebarOpen"
                class="absolute inset-0 z-20 bg-slate-900/40 md:hidden"
                @click="sidebarOpen = false"
            />

            <aside
                class="absolute inset-y-0 left-0 z-30 flex h-full w-64 flex-col border-r border-gray-100 bg-white px-2 pt-0 pb-4 transition-transform duration-200 ease-in-out dark:border-slate-800 dark:bg-slate-900 md:static md:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div v-if="user" class="mb-3 border-b border-gray-100 px-2 pt-2 pb-3 text-xs text-gray-500 dark:border-slate-800 dark:text-gray-400">
                    Ola, {{ user.name }}
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto pr-1">
                    <template v-for="menu in menus" :key="menu.id">
                        <div v-if="hasChildren(menu)" class="space-y-1">
                            <button :class="itemClass(menu)" @click="toggleGroup(menu.id)">
                                <i :class="[menu.icon, 'w-4 text-center']"></i>
                                <span class="truncate">{{ menu.name }}</span>
                                <i
                                    class="fas fa-chevron-down ml-auto text-xs text-gray-400 transition-transform"
                                    :class="{ 'rotate-180': openGroups[menu.id] }"
                                ></i>
                            </button>

                            <div v-show="openGroups[menu.id]" class="ml-3 space-y-1 border-l border-gray-100 pl-2 dark:border-slate-800">
                                <template v-for="child in menu.children" :key="child.id">
                                    <Link
                                        v-if="hasRoute(child)"
                                        :href="resolveHref(child)"
                                        :class="itemClass(child)"
                                        @click="closeMobileSidebar"
                                    >
                                        <i :class="[child.icon, 'w-4 text-center']"></i>
                                        <span class="truncate">{{ child.name }}</span>
                                    </Link>
                                </template>
                            </div>
                        </div>

                        <Link
                            v-else-if="hasRoute(menu)"
                            :href="resolveHref(menu)"
                            :class="itemClass(menu)"
                            @click="closeMobileSidebar"
                        >
                            <i :class="[menu.icon, 'w-4 text-center']"></i>
                            <span class="truncate">{{ menu.name }}</span>
                        </Link>
                    </template>
                </nav>

                <form class="mt-3 border-t border-gray-100 px-2 pt-3 dark:border-slate-800" @submit.prevent="$inertia.post('/logout')">
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800"
                    >
                        <i class="fas fa-sign-out-alt w-4 text-center"></i>
                        <span class="truncate">Sair</span>
                    </button>
                </form>
            </aside>

            <main class="flex-1 overflow-auto bg-gray-50 px-4 pb-4 pt-0 transition-colors dark:bg-slate-950">
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
