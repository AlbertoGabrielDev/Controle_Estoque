<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ContaForm from './Form.vue'

const props = defineProps({
  conta: { type: Object, required: true },
  contasPai: { type: Array, default: () => [] },
})

const form = useForm({
  codigo: props.conta?.codigo ?? '',
  nome: props.conta?.nome ?? '',
  tipo: props.conta?.tipo ?? 'ativo',
  conta_pai_id: props.conta?.conta_pai_id ?? '',
  aceita_lancamento: props.conta?.aceita_lancamento ?? true,
  ativo: props.conta?.ativo ?? true,
})

function submit() {
  form.put(route('contas_contabeis.update', props.conta.id))
}
</script>

<template>
  <Head title="Editar Conta Contábil" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Conta Contábil</h1>
    <Link :href="route('contas_contabeis.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ContaForm :form="form" :contas-pai="props.contasPai" submit-label="Salvar Alterações" @submit="submit" />
</template>
