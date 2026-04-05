<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FlowBreadcrumb from '../Shared/FlowBreadcrumb.vue'
import ItemsTable from '../Shared/ItemsTable.vue'
import StatusBadge from '../Shared/StatusBadge.vue'
import TotalsFooter from '../Shared/TotalsFooter.vue'

const props = defineProps({
  proposal: { type: Object, required: true },
})
const { t } = useI18n()

const convertForm = useForm({})

function send() {
  useForm({}).patch(route('commercial.proposals.send', props.proposal.id))
}
function approve() {
  useForm({}).patch(route('commercial.proposals.approve', props.proposal.id))
}
function reject() {
  useForm({}).patch(route('commercial.proposals.reject', props.proposal.id))
}
function convertToOrder() {
  convertForm.post(route('commercial.proposals.convertToOrder', props.proposal.id))
}

const canEdit = props.proposal.status === 'rascunho'
</script>

<template>
  <Head :title="`${t('Proposal')} ${proposal.numero}`" />

  <FlowBreadcrumb
    :opportunity="proposal.opportunity ?? null"
    :proposal="proposal"
    :order="proposal.sales_orders?.[0] ?? null"
  />

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ proposal.numero }}</h1>
    <div class="flex gap-2">
      <Link v-if="canEdit" :href="route('commercial.proposals.edit', proposal.id)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-100 rounded text-sm">{{ $t('Edit') }}</Link>
      <Link :href="route('commercial.proposals.index')" class="text-blue-600 dark:text-blue-400 text-sm py-2">{{ $t('Back') }}</Link>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded shadow p-4 space-y-2 dark:bg-slate-900 dark:border dark:border-slate-700 text-slate-700 dark:text-slate-200">
      <h3 class="font-semibold mb-3">{{ $t('Proposal Data') }}</h3>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Status') }}:</span> <StatusBadge :status="proposal.status" class="ml-1" /></div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Customer') }}:</span> {{ proposal.cliente?.nome_fantasia || proposal.cliente?.razao_social || $t('N/A') }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Issue Date') }}:</span> {{ proposal.data_emissao }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Valid Until') }}:</span> {{ proposal.validade_ate || $t('N/A') }}</div>
      <div v-if="proposal.observacoes" class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Notes') }}:</span> {{ proposal.observacoes }}</div>
    </div>

    <div class="bg-white rounded shadow p-4 dark:bg-slate-900 dark:border dark:border-slate-700">
      <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Actions') }}</h3>
      <div class="flex flex-wrap gap-2">
        <button v-if="proposal.status === 'rascunho'" @click="send" class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200 rounded text-sm hover:bg-blue-200">{{ $t('Send') }}</button>
        <button v-if="proposal.status === 'enviada'" @click="approve" class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-200 rounded text-sm hover:bg-green-200">{{ $t('Approve') }}</button>
        <button v-if="['enviada','aprovada'].includes(proposal.status)" @click="reject" class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200 rounded text-sm hover:bg-red-200">{{ $t('Reject') }}</button>
        <button v-if="proposal.status === 'aprovada'" @click="convertToOrder" :disabled="convertForm.processing" class="px-3 py-1 bg-cyan-600 text-white rounded text-sm hover:bg-cyan-700">{{ $t('Convert to Sales Order') }}</button>
      </div>

      <div class="mt-4">
        <TotalsFooter
          :subtotal="proposal.subtotal"
          :desconto-total="proposal.desconto_total"
          :total-impostos="proposal.total_impostos"
          :total="proposal.total"
        />
      </div>
    </div>
  </div>

  <!-- Items -->
  <div class="bg-white rounded shadow p-4 mb-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Items') }}</h3>
    <ItemsTable :items="proposal.items" />
  </div>

  <!-- Linked orders -->
  <div v-if="proposal.sales_orders?.length" class="bg-white rounded shadow p-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Generated Orders') }}</h3>
    <div v-for="o in proposal.sales_orders" :key="o.id" class="flex justify-between items-center border-b py-2 dark:border-slate-700 last:border-0">
      <span class="text-sm">{{ o.numero }} — <span class="capitalize">{{ o.status }}</span></span>
      <Link :href="route('commercial.orders.show', o.id)" class="text-blue-600 dark:text-blue-400 text-xs">{{ $t('View Order') }}</Link>
    </div>
  </div>
</template>
