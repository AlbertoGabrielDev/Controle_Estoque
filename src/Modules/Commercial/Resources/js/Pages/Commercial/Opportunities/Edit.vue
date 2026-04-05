<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import OpportunityForm from './OpportunityForm.vue'

const props = defineProps({
  opportunity: { type: Object, required: true },
  clientes_options: { type: Array, default: () => [] },
  users_options: { type: Array, default: () => [] },
})

const form = useForm({
  cliente_id: props.opportunity.cliente_id ?? '',
  nome: props.opportunity.nome ?? '',
  descricao: props.opportunity.descricao ?? '',
  origem: props.opportunity.origem ?? '',
  responsavel_id: props.opportunity.responsavel_id ?? '',
  valor_estimado: props.opportunity.valor_estimado ?? 0,
  data_prevista_fechamento: props.opportunity.data_prevista_fechamento ?? '',
  observacoes: props.opportunity.observacoes ?? '',
})

function submit() {
  form.patch(route('commercial.opportunities.update', props.opportunity.id))
}
</script>

<template>
  <Head :title="$t('Edit Opportunity')" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('Edit Opportunity') }}</h1>
    <Link :href="route('commercial.opportunities.show', opportunity.id)" class="text-blue-600 dark:text-blue-400">{{ $t('Back') }}</Link>
  </div>
  <OpportunityForm
    :form="form"
    :clientes-options="props.clientes_options"
    :users-options="props.users_options"
    :submit-label="$t('Save')"
    @submit="submit"
  />
</template>
