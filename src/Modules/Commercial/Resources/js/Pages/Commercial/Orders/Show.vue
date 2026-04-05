<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FlowBreadcrumb from '../Shared/FlowBreadcrumb.vue'
import ItemsTable from '../Shared/ItemsTable.vue'
import StatusBadge from '../Shared/StatusBadge.vue'
import TotalsFooter from '../Shared/TotalsFooter.vue'

const props = defineProps({ order: { type: Object, required: true } })
const { t } = useI18n()

const canEdit = props.order.status === 'rascunho'

function confirm() { useForm({}).patch(route('commercial.orders.confirm', props.order.id)) }
function cancel() {
  if (!window.confirm(t('Cancel this order?'))) return
  useForm({}).patch(route('commercial.orders.cancel', props.order.id))
}
</script>

<template>
  <Head :title="`${t('Sales Order')} ${order.numero}`" />

  <FlowBreadcrumb
    :opportunity="order.opportunity ?? null"
    :proposal="order.proposal ?? null"
    :order="order"
    :invoice="order.invoices?.[0] ?? null"
  />

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ order.numero }}</h1>
    <div class="flex gap-2">
      <Link v-if="canEdit" :href="route('commercial.orders.edit', order.id)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-100 rounded text-sm">{{ $t('Edit') }}</Link>
      <Link :href="route('commercial.orders.index')" class="text-blue-600 dark:text-blue-400 text-sm py-2">{{ $t('Back') }}</Link>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded shadow p-4 space-y-2 dark:bg-slate-900 dark:border dark:border-slate-700 text-slate-700 dark:text-slate-200">
      <h3 class="font-semibold mb-3">{{ $t('Order Data') }}</h3>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Status') }}:</span> <StatusBadge :status="order.status" class="ml-1" /></div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Customer') }}:</span> {{ order.cliente?.nome_fantasia || order.cliente?.razao_social || $t('N/A') }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Date') }}:</span> {{ order.data_pedido }}</div>
      <div v-if="order.observacoes" class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Notes') }}:</span> {{ order.observacoes }}</div>
    </div>

    <div class="bg-white rounded shadow p-4 dark:bg-slate-900 dark:border dark:border-slate-700">
      <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Actions') }}</h3>
      <div class="flex flex-wrap gap-2 mb-4">
        <button v-if="order.status === 'rascunho'" @click="confirm" class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-200 rounded text-sm hover:bg-green-200">{{ $t('Confirm') }}</button>
        <button v-if="['rascunho','confirmado','faturado_parcial'].includes(order.status)" @click="cancel" class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200 rounded text-sm hover:bg-red-200">{{ $t('Cancel') }}</button>
        <Link v-if="['confirmado','faturado_parcial'].includes(order.status)" :href="route('commercial.invoices.create') + `?order_id=${order.id}`" class="px-3 py-1 bg-cyan-600 text-white rounded text-sm hover:bg-cyan-700">{{ $t('Invoice') }}</Link>
      </div>
      <TotalsFooter
        :subtotal="order.subtotal"
        :desconto-total="order.desconto_total"
        :total-impostos="order.total_impostos"
        :total="order.total"
      />
    </div>
  </div>

  <!-- Items -->
  <div class="bg-white rounded shadow p-4 mb-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Items') }}</h3>
    <ItemsTable :items="order.items" :show-invoiced="true" />
  </div>

  <!-- Invoices -->
  <div v-if="order.invoices?.length" class="bg-white rounded shadow p-4 mb-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Invoices') }}</h3>
    <div v-for="inv in order.invoices" :key="inv.id" class="flex justify-between items-center border-b py-2 dark:border-slate-700 last:border-0">
      <span class="text-sm">{{ inv.numero }} — {{ inv.status }} — R$ {{ Number(inv.total).toFixed(2) }}</span>
      <Link :href="route('commercial.invoices.show', inv.id)" class="text-blue-600 dark:text-blue-400 text-xs">{{ $t('View') }}</Link>
    </div>
  </div>
</template>
