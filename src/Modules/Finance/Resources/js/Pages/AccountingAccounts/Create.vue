<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ContaForm from './Form.vue'

const props = defineProps({
  contasPai: { type: Array, default: () => [] },
})

const form = useForm({
  codigo: '',
  nome: '',
  tipo: 'ativo',
  conta_pai_id: '',
  aceita_lancamento: true,
  ativo: true,
})

function submit() {
  form.post(route('contas_contabeis.store'))
}
</script>

<template>
  <Head :title="$t('New Accounting Account')" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">{{ $t('New Accounting Account') }}</h1>
    <Link :href="route('contas_contabeis.index')" class="text-blue-600">{{ $t('Back') }}</Link>
  </div>

  <ContaForm
    :form="form"
    :contas-pai="props.contasPai"
    :submit-label="$t('Create')"
    @submit="submit"
  />
</template>
