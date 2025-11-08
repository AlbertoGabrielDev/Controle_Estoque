<script setup>
import { computed } from 'vue';
import useTheme from '@/composables/useTheme';

const { isDark, toggleTheme } = useTheme();

const label = computed(() => (isDark.value ? 'Ativar tema claro' : 'Ativar tema escuro'));
</script>

<template>
    <button
        type="button"
        :aria-pressed="isDark"
        :title="label"
        class="inline-flex items-center justify-center rounded-full p-2 text-gray-600 transition-colors duration-200
            hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-100
            dark:text-gray-300 dark:hover:bg-slate-700 dark:focus:ring-offset-slate-900"
        @click="toggleTheme"
    >
        <span class="sr-only">{{ label }}</span>
        <svg
            v-if="isDark"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-5 w-5"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364-6.364-1.06 1.06M6.697 17.303l-1.06 1.06m12.727 0-1.06-1.06M6.697 6.697l-1.06-1.06M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"
            />
        </svg>
        <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-5 w-5"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21 12.79A9 9 0 1111.21 3a7.5 7.5 0 009.79 9.79z"
            />
        </svg>
    </button>
</template>

