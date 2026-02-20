<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="h-4 w-4 rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-2 focus:ring-cyan-500/30 dark:border-slate-600 dark:bg-slate-800 dark:text-cyan-400 dark:focus:ring-cyan-400/30"
    />
</template>
