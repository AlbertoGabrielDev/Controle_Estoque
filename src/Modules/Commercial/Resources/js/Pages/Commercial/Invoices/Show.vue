<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FlowBreadcrumb from '../Shared/FlowBreadcrumb.vue'
import ItemsTable from '../Shared/ItemsTable.vue'
import StatusBadge from '../Shared/StatusBadge.vue'
import TotalsFooter from '../Shared/TotalsFooter.vue'

const props = defineProps({ invoice: { type: Object, required: true } })
const { t } = useI18n()

function issueInvoice() {
  useForm({}).patch(route('commercial.invoices.issue', props.invoice.id))
}

function cancelInvoice() {
  if (!window.confirm(t('Cancel this invoice?'))) return
  useForm({}).patch(route('commercial.invoices.cancel', props.invoice.id))
}
</script>

<template>
  <Head :title="`${t('Invoice')} ${invoice.numero}`" />

  <FlowBreadcrumb
    :opportunity="invoice.order?.opportunity ?? null"
    :proposal="invoice.order?.proposal ?? null"
    :order="invoice.order ?? null"
    :invoice="invoice"
    :sales-return="invoice.returns?.[0] ?? null"
    :receivable="invoice.receivables?.[0] ?? null"
  />

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ invoice.numero }}</h1>
    <div class="flex gap-2">
      <button v-if="invoice.status === 'emitida'" @click="issueInvoice" class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200 rounded text-sm hover:bg-blue-200">{{ $t('Confirm Issue') }}</button>
      <button v-if="['emitida','parcial'].includes(invoice.status)" @click="cancelInvoice" class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200 rounded text-sm hover:bg-red-200">{{ $t('Cancel') }}</button>
      <Link :href="route('commercial.invoices.index')" class="text-blue-600 dark:text-blue-400 text-sm py-2">{{ $t('Back') }}</Link>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded shadow p-4 space-y-2 dark:bg-slate-900 dark:border dark:border-slate-700 text-slate-700 dark:text-slate-200">
      <h3 class="font-semibold mb-3">{{ $t('Invoice Data') }}</h3>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Status') }}:</span> <StatusBadge :status="invoice.status" class="ml-1" /></div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Customer') }}:</span> {{ invoice.cliente?.nome_fantasia || invoice.cliente?.razao_social || $t('N/A') }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Sales Order') }}:</span> <Link v-if="invoice.order" :href="route('commercial.orders.show', invoice.order.id)" class="text-blue-600 dark:text-blue-400">{{ invoice.order.numero }}</Link><span v-else>{{ $t('N/A') }}</span></div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Issue Date') }}:</span> {{ invoice.data_emissao }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Due Date') }}:</span> {{ invoice.data_vencimento || $t('N/A') }}</div>
    </div>
    <div class="bg-white rounded shadow p-4 dark:bg-slate-900 dark:border dark:border-slate-700">
      <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Totals') }}</h3>
      <TotalsFooter
        :subtotal="invoice.subtotal"
        :desconto-total="invoice.desconto_total"
        :total-impostos="invoice.total_impostos"
        :total="invoice.total"
      />
    </div>
  </div>

  <div class="bg-white rounded shadow p-4 mb-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Invoiced Items') }}</h3>
    <ItemsTable :items="invoice.items" qty-field="quantidade_faturada" />
  </div>

  <div v-if="invoice.receivables?.length" class="bg-white rounded shadow p-4 mb-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Accounts Receivable') }}</h3>
    <div v-for="r in invoice.receivables" :key="r.id" class="flex justify-between text-sm border-b py-2 dark:border-slate-700 last:border-0">
      <span>{{ r.numero_documento }} — {{ $t('Due') }} {{ r.data_vencimento }} — <span class="capitalize">{{ r.status }}</span></span>
      <span class="font-medium">R$ {{ Number(r.valor_total).toFixed(2) }}</span>
    </div>
  </div>
</template>
