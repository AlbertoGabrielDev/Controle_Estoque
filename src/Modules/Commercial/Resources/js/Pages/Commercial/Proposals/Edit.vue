<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ProposalForm from './ProposalForm.vue'

const props = defineProps({
  proposal: { type: Object, required: true },
  clientes_options: { type: Array, default: () => [] },
  items_options: { type: Array, default: () => [] },
  unidades_options: { type: Array, default: () => [] },
  impostos_options: { type: Array, default: () => [] },
})

const form = useForm({
  cliente_id: props.proposal.cliente_id ?? '',
  validade_ate: props.proposal.validade_ate ?? '',
  observacoes: props.proposal.observacoes ?? '',
  total: props.proposal.total ?? 0,
  items: (props.proposal.items ?? []).map(i => ({
    _item_obj: null,
    item_id: i.item_id,
    descricao_snapshot: i.descricao_snapshot,
    unidade_medida_id: i.unidade_medida_id ?? '',
    _unidade_obj: null,
    _imposto_obj: null,
    imposto_id: i.imposto_id ?? '',
    quantidade: i.quantidade,
    preco_unit: i.preco_unit,
    desconto_percent: i.desconto_percent ?? 0,
    desconto_valor: i.desconto_valor ?? 0,
    aliquota_snapshot: i.aliquota_snapshot ?? 0,
    total_linha: i.total_linha,
  })),
})

function submit() {
  form.patch(route('commercial.proposals.update', props.proposal.id))
}
</script>

<template>
  <Head :title="$t('Edit Proposal')" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('Edit Proposal') }} {{ proposal.numero }}</h1>
    <Link :href="route('commercial.proposals.show', proposal.id)" class="text-blue-600 dark:text-blue-400">{{ $t('Back') }}</Link>
  </div>
  <ProposalForm
    :form="form"
    :clientes-options="props.clientes_options"
    :items-options="props.items_options"
    :unidades-options="props.unidades_options"
    :impostos-options="props.impostos_options"
    :submit-label="$t('Save')"
    @submit="submit"
  />
</template>
