import { computed, ref } from 'vue';

const STORAGE_KEY = 'controle-estoque:theme';
const theme = ref('light');
let initialized = false;
let mediaQuery;

const readStoredPreference = () => {
    if (typeof window === 'undefined') return null;
    try {
        return window.localStorage.getItem(STORAGE_KEY);
    } catch (error) {
        console.warn('Não foi possível ler a preferência de tema armazenada.', error);
        return null;
    }
};

const writeStoredPreference = (value) => {
    if (typeof window === 'undefined') return;
    try {
        window.localStorage.setItem(STORAGE_KEY, value);
    } catch (error) {
        console.warn('Não foi possível salvar a preferência de tema.', error);
    }
};

const applyTheme = (value) => {
    if (typeof document === 'undefined') return;
    const isDark = value === 'dark';
    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.dataset.theme = value;
    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';

    if (!document.body) {
        document.addEventListener(
            'DOMContentLoaded',
            () => applyTheme(value),
            { once: true }
        );
        return;
    }

    document.body.classList.toggle('dark', isDark);
    document.body.dataset.theme = value;
    document.body.style.colorScheme = isDark ? 'dark' : 'light';
};

const setTheme = (value, persist = true) => {
    const normalized = value === 'dark' ? 'dark' : 'light';
    theme.value = normalized;

    if (persist) {
        writeStoredPreference(normalized);
    }

    applyTheme(normalized);
};

const handleSystemChange = (event) => {
    const stored = readStoredPreference();
    if (stored === 'dark' || stored === 'light') {
        return;
    }

    setTheme(event.matches ? 'dark' : 'light', false);
};

export const initializeTheme = () => {
    if (initialized || typeof window === 'undefined') {
        return;
    }

    initialized = true;

    const stored = readStoredPreference();
    if (stored === 'dark' || stored === 'light') {
        setTheme(stored, false);
    } else {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        setTheme(prefersDark ? 'dark' : 'light', false);
    }

    mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    if (mediaQuery) {
        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', handleSystemChange);
        } else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(handleSystemChange);
        }
    }
};

const toggleTheme = () => {
    setTheme(theme.value === 'dark' ? 'light' : 'dark');
};

export default function useTheme() {
    if (!initialized && typeof window !== 'undefined') {
        initializeTheme();
    }

    const isDark = computed(() => theme.value === 'dark');

    return {
        theme,
        isDark,
        setTheme: (value) => setTheme(value, true),
        toggleTheme,
    };
}
