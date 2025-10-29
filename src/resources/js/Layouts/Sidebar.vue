<template>
    <div class="h-screen flex flex-col">
        <header class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
            <div class="flex items-center">
                <!-- ...logo e título... -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 mr-2" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.520.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
                <h1 class="text-xl font-bold text-gray-800">WhatsApp Marketing Dashboard</h1>
            </div>
            <!-- Botão menu mobile -->
            <button class="md:hidden ml-4" @click="sidebarOpen = !sidebarOpen">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <!-- ...usuário... -->
            <div class="hidden md:flex items-center">
                <div class="relative mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="notification-badge">3</span>
                </div>
                <div class="flex items-center">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0iY3VycmVudENvbG9yIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA1djguMDA0YTMuMDEgMy4wMSAwIDAgMS0zLjAxIDMuMDFoLTYuOTczYTMuMDEgMy4wMSAwIDAgMS0zLjAxLTMuMDFWNWg3LjQ5N2wxLjUgMS41SDEzLjk5N3ptLTEwLTIuNWEuNS41IDAgMCAwLS41LjV2LjVoNS41ODZsLTEuNS0xLjVIMTMuNWEuNS41IDAgMCAwLS41LS41aC01eiIgY2xpcC1ydWxlPSJldmVub2RkIi8+PC9zdmc+"
                        class="h-8 w-8 rounded-full bg-gray-300 p-1" alt="User">
                    <span class="ml-2 text-gray-700 font-medium">Admin</span>
                </div>
            </div>
        </header>

        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar Desktop/Mobile Unificado -->
            <transition name="fade">
                <div v-if="sidebarOpen || isDesktop"
                    class="sidebar bg-green-800 text-white flex-col w-64 fixed md:static z-40 h-full"
                    :class="{ 'hidden md:flex': !sidebarOpen && isDesktop }">
                    <!-- Botão fechar só no mobile -->
                    <button v-if="!isDesktop" class="self-end m-4" @click="sidebarOpen = false">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <nav class="flex-1 px-2 py-4">
                        <ul>
                            <li v-for="menu in menus" :key="menu.key" class="mb-2">
                                <button @click="handleMenuClick(menu)" :class="{ 'bg-green-700': isMenuActive(menu) }"
                                    class="tab-btn flex items-center w-full px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    <span class="mr-2" v-html="menu.icon"></span>
                                    <span :class="isDesktop ? 'hidden md:inline' : ''">{{ menu.label }}</span>
                                    <svg v-if="menu.submenus" class="ml-auto" width="16" height="16" fill="none"
                                        stroke="currentColor">
                                        <path d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>
                                <ul v-if="menu.submenus && openSubmenu === menu.key" class="ml-6 mt-1">
                                    <li v-for="submenu in menu.submenus" :key="submenu.key">
                                        <button @click="handleSubmenuClick(submenu)"
                                            :class="{ 'bg-green-700': isSubmenuActive(submenu) }"
                                            class="flex items-center px-2 py-1 text-sm w-full rounded hover:bg-green-700">
                                            <span>{{ submenu.label }}</span>
                                        </button>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                    <div class="p-4">
                        <div class="bg-green-700 rounded-lg p-3 text-center">
                            <p class="text-sm">Plano Atual</p>
                            <p class="font-bold">Premium</p>
                            <p class="text-xs mt-1">Válido até 15/12/2023</p>
                        </div>
                    </div>
                </div>
            </transition>
            <!-- Conteúdo principal -->
            <div class="flex-1 overflow-y-auto bg-gray-100">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
    props: {
        activeTab: String
    },
    data() {
        return {
            sidebarOpen: false,
            openSubmenu: null,
            isDesktop: window.innerWidth >= 768,
            menus: [
                {
                    key: 'dashboard',
                    label: 'Dashboard',
                    tab: 'dashboard',
                    route: '/verdurao/bot/dashboard',
                    icon: `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:mr-3" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        `
                },
                {
                    key: 'bulk',
                    label: 'Envio em Massa',
                    tab: 'bulk',
                    route: '/verdurao/bot',
                    icon: `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:mr-3" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
            </svg>
        `
                },
                {
                    key: 'auto',
                    label: 'Respostas Automáticas',
                    tab: 'auto',
                    route: '/verdurao/auto',
                    icon: `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:mr-3" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        `
                },
                {
                    key: 'extract',
                    label: 'Extração do Maps',
                    tab: 'extract',
                    route: '/verdurao/business-extractor',
                    icon: `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:mr-3" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        `
                },
                {
                    key: 'contacts',
                    label: 'Contatos',
                    tab: 'contacts',
                    route: '/verdurao/whatsapp/contacts',
                    icon: `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:mr-3" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        `
                },
                {
                    key: 'settings',
                    label: 'Configurações',
                    tab: 'settings',
                    icon: `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:mr-3" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        `,
                    submenus: [
                        {
                            key: 'modelos-mensagem',
                            label: 'Modelos de Mensagem',
                            route: '/verdurao/configuracoes/modelos-mensagem'
                        }
                        // Adicione outros submenus aqui depois!
                    ]
                }
            ]

        }
    },
    mounted() {
        window.addEventListener('resize', this.handleResize);
        this.handleResize();
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize);
    },
    methods: {
        handleMenuClick(menu) {
            if (menu.submenus) {
                // Expande/recolhe submenu
                this.openSubmenu = (this.openSubmenu === menu.key) ? null : menu.key;
                // Se quiser marcar como ativo, pode: this.$emit('setActiveTab', menu.tab);
            } else {
                // Redireciona
                router.visit(menu.route);
                this.sidebarOpen = false;
                this.openSubmenu = null;
                // Marcar tab ativa, se precisar: this.$emit('setActiveTab', menu.tab);
            }
        },
        handleSubmenuClick(submenu) {
            router.visit(submenu.route);
            this.sidebarOpen = false;
            // Aqui também você pode emitir evento se quiser atualizar tab ativa
        },
        goToSettings() {
            // Mantido para compatibilidade com menu mobile antigo, se quiser remover, pode!
            this.handleMenuClick(this.menus.find(m => m.key === 'settings'));
        },
        isMenuActive(menu) {
            // Pode adaptar: tab ativa, pathname, etc
            if (menu.submenus) {
                // Se algum submenu está na rota, menu pai fica ativo
                return menu.submenus.some(sub => window.location.pathname === sub.route);
            }
            return window.location.pathname === menu.route;
        },
        isSubmenuActive(submenu) {
            return window.location.pathname === submenu.route;
        },
        handleResize() {
            this.isDesktop = window.innerWidth >= 768;
            if (this.isDesktop) this.sidebarOpen = false;
        },
    }
}
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>