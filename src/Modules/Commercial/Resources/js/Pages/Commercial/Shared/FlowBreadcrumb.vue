<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  opportunity: { type: Object, default: null },
  proposal: { type: Object, default: null },
  order: { type: Object, default: null },
  invoice: { type: Object, default: null },
  salesReturn: { type: Object, default: null },
  receivable: { type: Object, default: null },
})
const { t } = useI18n()

/**
 * Build the commercial flow trail from available entities.
 *
 * @returns {Array<{key: string, label: string, routeName: string|null, id: number|null}>}
 */
function trail() {
  return [
    { key: 'opportunity', label: t('Opportunity'), routeName: 'commercial.opportunities.show', id: props.opportunity?.id ?? null },
    { key: 'proposal', label: t('Proposal'), routeName: 'commercial.proposals.show', id: props.proposal?.id ?? null },
    { key: 'order', label: t('Sales Order'), routeName: 'commercial.orders.show', id: props.order?.id ?? null },
    { key: 'invoice', label: t('Invoice'), routeName: 'commercial.invoices.show', id: props.invoice?.id ?? null },
    { key: 'salesReturn', label: t('Sales Return'), routeName: 'commercial.returns.show', id: props.salesReturn?.id ?? null },
    { key: 'receivable', label: t('Accounts Receivable'), routeName: 'commercial.receivables.show', id: props.receivable?.id ?? null },
  ]
}
</script>

<template>
  <nav class="mb-4 text-xs text-slate-500 dark:text-slate-400" :aria-label="t('Commercial Flow')">
    <ol class="flex flex-wrap items-center gap-2">
      <li v-for="(step, index) in trail()" :key="step.key" class="flex items-center gap-2">
        <Link
          v-if="step.id && step.routeName"
          :href="route(step.routeName, step.id)"
          class="font-medium text-blue-600 hover:underline dark:text-blue-400"
        >
          {{ step.label }}
        </Link>
        <span v-else class="font-medium text-slate-400 dark:text-slate-500">{{ step.label }}</span>
        <span v-if="index < trail().length - 1" class="text-slate-300">/</span>
      </li>
    </ol>
  </nav>
</template>
