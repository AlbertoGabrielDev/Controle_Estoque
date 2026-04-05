<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import FlowBreadcrumb from '../Shared/FlowBreadcrumb.vue'
import StatusBadge from '../Shared/StatusBadge.vue'

const props = defineProps({
  opportunity: { type: Object, required: true },
})
const { t } = useI18n()

const statusForm = useForm({ status: '', motivo_perda: '' })
const showStatusModal = ref(false)
const pendingStatus = ref('')

const statusLabels = {
  novo: 'New',
  em_contato: 'In Contact',
  proposta_enviada: 'Proposal Sent',
  negociacao: 'Negotiation',
  ganho: 'Won',
  perdido: 'Lost',
}

function openStatusChange(status) {
  pendingStatus.value = status
  statusForm.status = status
  statusForm.motivo_perda = ''
  showStatusModal.value = true
}

function submitStatus() {
  statusForm.patch(route('commercial.opportunities.status', props.opportunity.id), {
    onSuccess: () => { showStatusModal.value = false },
  })
}
</script>

<template>
  <Head :title="`${t('Opportunity')} ${opportunity.codigo}`" />

  <FlowBreadcrumb
    :opportunity="opportunity"
    :proposal="opportunity.proposals?.[0] ?? null"
    :order="opportunity.sales_orders?.[0] ?? null"
  />

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ opportunity.codigo }} — {{ opportunity.nome }}</h1>
    <div class="flex gap-2">
      <Link :href="route('commercial.opportunities.edit', opportunity.id)" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-100 rounded text-sm">{{ $t('Edit') }}</Link>
      <Link :href="route('commercial.opportunities.index')" class="text-blue-600 dark:text-blue-400 text-sm py-2">{{ $t('Back') }}</Link>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded shadow p-4 space-y-2 dark:bg-slate-900 dark:border dark:border-slate-700 text-slate-700 dark:text-slate-200">
      <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">{{ $t('Opportunity Data') }}</h3>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Status') }}:</span> <StatusBadge :status="opportunity.status" class="ml-1" /></div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Customer') }}:</span> {{ opportunity.cliente?.nome_fantasia || opportunity.cliente?.razao_social || opportunity.cliente?.nome || $t('N/A') }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Responsible') }}:</span> {{ opportunity.responsavel?.name || $t('N/A') }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Estimated Value') }}:</span> R$ {{ Number(opportunity.valor_estimado).toFixed(2) }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Expected Close') }}:</span> {{ opportunity.data_prevista_fechamento || $t('N/A') }}</div>
      <div class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Origin') }}:</span> {{ opportunity.origem || $t('N/A') }}</div>
      <div v-if="opportunity.motivo_perda" class="text-sm text-red-600"><span class="font-medium">{{ $t('Loss Reason') }}:</span> {{ opportunity.motivo_perda }}</div>
      <div v-if="opportunity.descricao" class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Description') }}:</span> {{ opportunity.descricao }}</div>
      <div v-if="opportunity.observacoes" class="text-sm"><span class="text-slate-500 dark:text-slate-400">{{ $t('Notes') }}:</span> {{ opportunity.observacoes }}</div>
    </div>

    <div class="bg-white rounded shadow p-4 dark:bg-slate-900 dark:border dark:border-slate-700">
      <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">{{ $t('Status Actions') }}</h3>
      <div v-if="!['ganho','perdido'].includes(opportunity.status)" class="flex flex-wrap gap-2">
        <button v-if="opportunity.status !== 'em_contato'" @click="openStatusChange('em_contato')" class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200 rounded text-sm hover:bg-blue-200">{{ $t('In Contact') }}</button>
        <button v-if="opportunity.status !== 'negociacao'" @click="openStatusChange('negociacao')" class="px-3 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-200 rounded text-sm hover:bg-yellow-200">{{ $t('Negotiation') }}</button>
        <button @click="openStatusChange('ganho')" class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-200 rounded text-sm hover:bg-green-200">{{ $t('Mark as Won') }}</button>
        <button @click="openStatusChange('perdido')" class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200 rounded text-sm hover:bg-red-200">{{ $t('Mark as Lost') }}</button>
      </div>
      <div v-else class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Opportunity closed.') }}</div>

      <div v-if="!['ganho','perdido'].includes(opportunity.status)" class="mt-4">
        <Link
          :href="route('commercial.opportunities.convertToProposal', opportunity.id)"
          method="post"
          as="button"
          class="px-3 py-2 bg-cyan-600 text-white rounded text-sm hover:bg-cyan-700"
        >
          {{ $t('Create Proposal') }}
        </Link>
      </div>
    </div>
  </div>

  <!-- Proposals linked -->
  <div v-if="opportunity.proposals?.length" class="bg-white rounded shadow p-4 mb-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <h3 class="font-semibold mb-3 text-slate-700 dark:text-slate-200">{{ $t('Proposals') }}</h3>
    <table class="w-full text-sm text-slate-700 dark:text-slate-200">
      <thead class="bg-slate-50 dark:bg-slate-800/70"><tr>
        <th class="px-3 py-2 text-left">{{ $t('Number') }}</th>
        <th class="px-3 py-2 text-left">{{ $t('Status') }}</th>
        <th class="px-3 py-2 text-left">{{ $t('Total') }}</th>
        <th class="px-3 py-2 text-left"></th>
      </tr></thead>
      <tbody>
        <tr v-for="p in opportunity.proposals" :key="p.id" class="border-t dark:border-slate-700">
          <td class="px-3 py-2">{{ p.numero }}</td>
          <td class="px-3 py-2">{{ p.status }}</td>
          <td class="px-3 py-2">R$ {{ Number(p.total).toFixed(2) }}</td>
          <td class="px-3 py-2"><Link :href="route('commercial.proposals.show', p.id)" class="text-blue-600 dark:text-blue-400 text-xs">{{ $t('View') }}</Link></td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Status change modal -->
  <div v-if="showStatusModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white dark:bg-slate-900 dark:border dark:border-slate-700 rounded-lg shadow-xl p-6 w-full max-w-md text-slate-700 dark:text-slate-200">
      <h3 class="font-semibold mb-4">{{ $t('Change Status') }}</h3>
      <div v-if="pendingStatus === 'perdido'" class="mb-4">
        <label class="block text-sm font-medium mb-1">{{ $t('Loss Reason') }} *</label>
        <textarea v-model="statusForm.motivo_perda" rows="3" class="border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" required></textarea>
        <div v-if="statusForm.errors.motivo_perda" class="text-red-600 text-sm mt-1">{{ statusForm.errors.motivo_perda }}</div>
      </div>
      <p v-else class="text-sm mb-4">{{ $t('Confirm status change to "{status}"?', { status: $t(statusLabels[pendingStatus]) }) }}</p>
      <div class="flex justify-end gap-2">
        <button @click="showStatusModal = false" class="px-3 py-2 bg-gray-100 dark:bg-slate-800 dark:text-slate-100 rounded text-sm">{{ $t('Cancel') }}</button>
        <button @click="submitStatus" :disabled="statusForm.processing" class="px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">{{ $t('Confirm') }}</button>
      </div>
    </div>
  </div>
</template>
