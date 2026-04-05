<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  subtotal: { type: [Number, String], default: 0 },
  descontoTotal: { type: [Number, String], default: 0 },
  totalImpostos: { type: [Number, String], default: 0 },
  total: { type: [Number, String], default: 0 },
})
const { t } = useI18n()

/**
 * Format a number as BRL currency.
 *
 * @param {number|string} value
 * @returns {string}
 */
function money(value) {
  return Number(value || 0).toFixed(2)
}

const subtotalLabel = computed(() => money(props.subtotal))
const descontoLabel = computed(() => money(props.descontoTotal))
const impostosLabel = computed(() => money(props.totalImpostos))
const totalLabel = computed(() => money(props.total))
</script>

<template>
  <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm text-slate-700 dark:text-slate-200">
    <span class="text-slate-500 dark:text-slate-400">{{ t('Subtotal') }}</span>
    <span class="text-right">R$ {{ subtotalLabel }}</span>
    <span class="text-slate-500 dark:text-slate-400">{{ t('Discount') }}</span>
    <span class="text-right text-red-600">- R$ {{ descontoLabel }}</span>
    <span class="text-slate-500 dark:text-slate-400">{{ t('Taxes') }}</span>
    <span class="text-right">R$ {{ impostosLabel }}</span>
    <span class="font-semibold border-t border-slate-200 dark:border-slate-700 pt-1">{{ t('Total') }}</span>
    <span class="text-right font-bold border-t border-slate-200 dark:border-slate-700 pt-1">R$ {{ totalLabel }}</span>
  </div>
</template>
