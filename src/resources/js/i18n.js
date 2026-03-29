import { createI18n } from 'vue-i18n';
import pt from './locales/pt.json';
import en from './locales/en.json';
import es from './locales/es.json';

const messages = { pt, en, es };

/**
 * Detect locale based on priority:
 * 1. localStorage (User's explicit choice)
 * 2. navigator.language (Browser preference)
 * 3. preferredLocale (Inertia prop as fallback)
 * 4. default 'pt'
 */
function detectLocale(preferredLocale) {
    const stored = localStorage.getItem('locale');
    if (stored && ['pt', 'en', 'es'].includes(stored)) {
        return stored;
    }

    const browser = navigator.language.split('-')[0];
    if (['pt', 'en', 'es'].includes(browser)) {
        return browser;
    }

    if (preferredLocale && ['pt', 'en', 'es'].includes(preferredLocale)) {
        return preferredLocale;
    }

    return 'pt';
}

export function createI18nInstance(preferredLocale) {
    const locale = detectLocale(preferredLocale);
    
    // Persist to localStorage if it's the first time or different
    if (localStorage.getItem('locale') !== locale) {
        localStorage.setItem('locale', locale);
    }

    // Set document lang
    document.documentElement.lang = locale;

    return createI18n({
        legacy: false, // Use Composition API
        locale: locale,
        fallbackLocale: 'en',
        messages,
    });
}

/**
 * Change locale purely in frontend
 */
export function changeLocale(i18n, target) {
    if (!['pt', 'en', 'es'].includes(target)) return;

    if (i18n.global.locale.value) {
        i18n.global.locale.value = target;
    } else {
        // Compatibility for different i18n versions if needed
        i18n.global.locale = target;
    }
    
    localStorage.setItem('locale', target);
    document.documentElement.lang = target;
}
