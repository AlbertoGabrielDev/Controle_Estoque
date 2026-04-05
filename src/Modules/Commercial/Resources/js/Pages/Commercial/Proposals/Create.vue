<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ProposalForm from './ProposalForm.vue'

const props = defineProps({
  clientes_options: { type: Array, default: () => [] },
  items_options: { type: Array, default: () => [] },
  unidades_options: { type: Array, default: () => [] },
  impostos_options: { type: Array, default: () => [] },
  opportunity_id: { type: Number, default: null },
})

const form = useForm({
  opportunity_id: props.opportunity_id ?? '',
  cliente_id: '',
  data_emissao: new Date().toISOString().split('T')[0],
  validade_ate: '',
  observacoes: '',
  total: 0,
  items: [],
})

function submit() {
  form.post(route('commercial.proposals.store'))
}
</script>

<template>
  <Head :title="$t('New Proposal')" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('New Commercial Proposal') }}</h1>
    <Link :href="route('commercial.proposals.index')" class="text-blue-600 dark:text-blue-400">{{ $t('Back') }}</Link>
  </div>
  <ProposalForm
    :form="form"
    :clientes-options="props.clientes_options"
    :items-options="props.items_options"
    :unidades-options="props.unidades_options"
    :impostos-options="props.impostos_options"
    :submit-label="$t('Create Proposal')"
    @submit="submit"
  />
</template>
