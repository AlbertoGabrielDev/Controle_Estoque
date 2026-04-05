<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  status: { type: String, default: '' },
})
const { t } = useI18n()

/**
 * Resolve display label for the provided status.
 *
 * @param {string} status
 * @returns {string}
 */
function resolveLabel(status) {
  const map = {
    novo: t('New'),
    em_contato: t('In Contact'),
    proposta_enviada: t('Proposal Sent'),
    negociacao: t('Negotiation'),
    ganho: t('Won'),
    perdido: t('Lost'),
    rascunho: t('Draft'),
    enviada: t('Sent'),
    aprovada: t('Approved'),
    rejeitada: t('Rejected'),
    vencida: t('Expired'),
    convertida: t('Converted'),
    confirmado: t('Confirmed'),
    faturado_parcial: t('Partially Invoiced'),
    faturado_total: t('Fully Invoiced'),
    cancelado: t('Canceled'),
    fechado: t('Closed'),
    emitida: t('Issued'),
    parcial: t('Partial'),
    paga: t('Paid'),
    estornada: t('Reversed'),
    aberta: t('Open'),
    confirmada: t('Confirmed'),
    aberto: t('Open'),
    recebido: t('Received'),
  }

  return map[status] ?? status ?? t('N/A')
}

const classes = computed(() => {
  const status = props.status

  if (['ganho', 'aprovada', 'confirmado', 'faturado_total', 'paga', 'confirmada', 'recebido'].includes(status)) {
    return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200'
  }

  if (['rejeitada', 'perdido', 'cancelado', 'estornada'].includes(status)) {
    return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200'
  }

  if (['enviada', 'proposta_enviada', 'faturado_parcial', 'parcial'].includes(status)) {
    return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200'
  }

  if (['negociacao', 'vencida'].includes(status)) {
    return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200'
  }

  return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200'
})

const label = computed(() => resolveLabel(props.status))
</script>

<template>
  <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" :class="classes">
    {{ label }}
  </span>
</template>
