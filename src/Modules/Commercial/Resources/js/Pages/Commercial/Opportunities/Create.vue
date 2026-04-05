<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import OpportunityForm from './OpportunityForm.vue'

const props = defineProps({
  clientes_options: { type: Array, default: () => [] },
  users_options: { type: Array, default: () => [] },
})

const form = useForm({
  cliente_id: '',
  nome: '',
  descricao: '',
  origem: '',
  responsavel_id: '',
  valor_estimado: 0,
  data_prevista_fechamento: '',
  observacoes: '',
})

function submit() {
  form.post(route('commercial.opportunities.store'))
}
</script>

<template>
  <Head :title="$t('New Opportunity')" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('New Opportunity') }}</h1>
    <Link :href="route('commercial.opportunities.index')" class="text-blue-600 dark:text-blue-400">{{ $t('Back') }}</Link>
  </div>
  <OpportunityForm
    :form="form"
    :clientes-options="props.clientes_options"
    :users-options="props.users_options"
    :submit-label="$t('Create Opportunity')"
    @submit="submit"
  />
</template>
