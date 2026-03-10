<template>
    <button
        class="inline-flex items-center justify-center rounded-xl border border-cyan-600 bg-cyan-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition-all duration-150 hover:-translate-y-0.5 hover:bg-cyan-500 hover:shadow focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 active:translate-y-0 active:bg-cyan-700 disabled:cursor-not-allowed disabled:opacity-60 dark:border-cyan-500 dark:bg-cyan-500 dark:text-slate-950 dark:hover:bg-cyan-400 dark:focus:ring-cyan-400"
    >
        <slot />
    </button>
</template>
