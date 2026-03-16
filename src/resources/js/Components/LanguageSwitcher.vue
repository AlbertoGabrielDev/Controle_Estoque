<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { changeLocale } from '../i18n';

const page = usePage();
const { locale: i18nLocale } = useI18n();

const currentLocale = computed(() => page.props.locale || 'pt');
const availableLocales = computed(() => page.props.available_locales || ['pt', 'en', 'es']);

const languages = [
    { code: 'pt', name: 'Português', flag: '🇧🇷' },
    { code: 'en', name: 'English', flag: '🇺🇸' },
    { code: 'es', name: 'Español', flag: '🇪🇸' },
].filter(l => availableLocales.value.includes(l.code));

const currentLanguage = computed(() => 
    languages.find(l => l.code === i18nLocale.value) || languages[0]
);

const handleLocaleChange = (code) => {
    // 1. Update Frontend state (instant for $t calls)
    i18nLocale.value = code;
    localStorage.setItem('locale', code);
    document.documentElement.lang = code;

    // 2. Persist to Backend and Refresh all server-side props (menus, etc.)
    router.post(route('locale.update'), { locale: code }, {
        preserveState: false, // Force a fresh state from the server
        preserveScroll: true,
        onSuccess: () => {
             // Backend update success
        }
    });
};
</script>

<template>
    <div class="relative group">
        <button class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors">
            <span>{{ currentLanguage.flag }}</span>
            <span class="hidden sm:inline">{{ currentLanguage.name }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
            <div class="py-1">
                <button
                    v-for="lang in languages"
                    :key="lang.code"
                    @click="handleLocaleChange(lang.code)"
                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    :class="{ 'bg-gray-50 dark:bg-gray-800': lang.code === i18nLocale.value }"
                >
                    <span>{{ lang.flag }}</span>
                    <span>{{ lang.name }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
