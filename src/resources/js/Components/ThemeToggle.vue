<script setup>
import { computed } from 'vue'
import useTheme from '@/composables/useTheme'

const { isDark, toggleTheme } = useTheme()

const label = computed(() => (isDark.value ? 'Ativar tema claro' : 'Ativar tema escuro'))
</script>

<template>
    <button
        type="button"
        :aria-pressed="isDark"
        :title="label"
        class="theme-toggle inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-slate-50 hover:shadow md:h-11 md:w-11 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
        @click="toggleTheme"
    >
        <span class="sr-only">{{ label }}</span>

        <transition name="theme-icon" mode="out-in">
            <svg
                v-if="isDark"
                key="sun"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.7"
                stroke="currentColor"
                class="h-5 w-5 text-amber-400"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364-6.364-1.06 1.06M6.697 17.303l-1.06 1.06m12.727 0-1.06-1.06M6.697 6.697l-1.06-1.06M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"
                />
            </svg>

            <svg
                v-else
                key="moon"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.7"
                stroke="currentColor"
                class="h-5 w-5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21 12.79A9 9 0 1111.21 3a7.5 7.5 0 009.79 9.79z"
                />
            </svg>
        </transition>
    </button>
</template>

<style scoped>
.theme-icon-enter-active,
.theme-icon-leave-active {
    transition: all 0.18s ease;
}

.theme-icon-enter-from,
.theme-icon-leave-to {
    opacity: 0;
    transform: scale(0.85) rotate(-10deg);
}
</style>
