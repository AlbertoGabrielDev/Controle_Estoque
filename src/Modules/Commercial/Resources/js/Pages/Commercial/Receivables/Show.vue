<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FlowBreadcrumb from '../Shared/FlowBreadcrumb.vue'
import StatusBadge from '../Shared/StatusBadge.vue'

const props = defineProps({ receivable: { type: Object, required: true } })
const { t } = useI18n()
</script>

<template>
  <Head :title="`${t('Accounts Receivable')} ${receivable.numero_documento}`" />

  <FlowBreadcrumb
    :opportunity="receivable.order?.opportunity ?? null"
    :proposal="receivable.order?.proposal ?? null"
    :order="receivable.order ?? null"
    :invoice="receivable.invoice ?? null"
    :receivable="receivable"
  />

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ receivable.numero_documento }}</h1>
    <Link :href="route('commercial.receivables.index')" class="text-blue-600 dark:text-blue-400 text-sm">{{ $t('Back') }}</Link>
  </div>

  <div class="bg-white rounded shadow p-4 space-y-2 dark:bg-slate-900 dark:border dark:border-slate-700 max-w-lg text-slate-700 dark:text-slate-200">
    <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Status') }}:</span> <StatusBadge :status="receivable.status" class="ml-1" /></div>
    <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Customer') }}:</span> {{ receivable.cliente?.nome_fantasia || receivable.cliente?.razao_social || $t('N/A') }}</div>
    <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Issue Date') }}:</span> {{ receivable.data_emissao }}</div>
    <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Due Date') }}:</span> {{ receivable.data_vencimento }}</div>
    <div class="text-sm font-semibold"><span class="text-slate-500 dark:text-slate-400">{{ $t('Value') }}:</span> R$ {{ Number(receivable.valor_total).toFixed(2) }}</div>
    <div v-if="receivable.invoice" class="text-sm">
      <span class="text-slate-500 dark:text-slate-400">{{ $t('Invoice') }}:</span>
      <Link :href="route('commercial.invoices.show', receivable.invoice.id)" class="text-blue-600 dark:text-blue-400 ml-1">{{ receivable.invoice.numero }}</Link>
    </div>
    <div v-if="receivable.order" class="text-sm">
      <span class="text-slate-500 dark:text-slate-400">{{ $t('Sales Order') }}:</span>
      <Link :href="route('commercial.orders.show', receivable.order.id)" class="text-blue-600 dark:text-blue-400 ml-1">{{ receivable.order.numero }}</Link>
    </div>
  </div>
</template>
