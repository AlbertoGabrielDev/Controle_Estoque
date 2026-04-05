<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FlowBreadcrumb from '../Shared/FlowBreadcrumb.vue'
import ItemsTable from '../Shared/ItemsTable.vue'
import StatusBadge from '../Shared/StatusBadge.vue'

const props = defineProps({ return: { type: Object, required: true } })
const { t } = useI18n()
const salesReturn = props.return

function confirmReturn() {
  if (!window.confirm(t('Confirm this return? This will adjust receivables.'))) return
  useForm({}).patch(route('commercial.returns.confirm', salesReturn.id))
}
function cancelReturn() {
  if (!window.confirm(t('Cancel this return?'))) return
  useForm({}).patch(route('commercial.returns.cancel', salesReturn.id))
}
</script>

<template>
  <Head :title="`${t('Sales Return')} ${salesReturn.numero}`" />

  <FlowBreadcrumb
    :opportunity="salesReturn.order?.opportunity ?? null"
    :proposal="salesReturn.order?.proposal ?? null"
    :order="salesReturn.order ?? null"
    :invoice="salesReturn.invoice ?? null"
    :sales-return="salesReturn"
  />

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ salesReturn.numero }}</h1>
    <div class="flex gap-2">
      <button v-if="salesReturn.status === 'aberta'" @click="confirmReturn" class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-200 rounded text-sm hover:bg-green-200">{{ $t('Confirm') }}</button>
      <button v-if="salesReturn.status === 'aberta'" @click="cancelReturn" class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200 rounded text-sm hover:bg-red-200">{{ $t('Cancel') }}</button>
      <Link :href="route('commercial.returns.index')" class="text-blue-600 dark:text-blue-400 text-sm py-2">{{ $t('Back') }}</Link>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded shadow p-4 space-y-2 dark:bg-slate-900 dark:border dark:border-slate-700 text-slate-700 dark:text-slate-200">
      <h3 class="font-semibold mb-3">{{ $t('Return Data') }}</h3>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Status') }}:</span> <StatusBadge :status="salesReturn.status" class="ml-1" /></div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Customer') }}:</span> {{ salesReturn.cliente?.nome_fantasia || salesReturn.cliente?.razao_social || $t('N/A') }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Date') }}:</span> {{ salesReturn.data_devolucao }}</div>
      <div v-if="salesReturn.invoice" class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Invoice') }}:</span> <Link :href="route('commercial.invoices.show', salesReturn.invoice.id)" class="text-blue-600 dark:text-blue-400">{{ salesReturn.invoice.numero }}</Link></div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Reason') }}:</span> {{ salesReturn.motivo }}</div>
    </div>
  </div>

  <div class="bg-white rounded shadow p-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Returned Items') }}</h3>
    <ItemsTable :items="salesReturn.items" qty-field="quantidade_devolvida" />
  </div>
</template>
