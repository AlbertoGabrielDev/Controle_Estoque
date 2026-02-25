<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ExpenseForm from './Form.vue'

const props = defineProps({
  centrosCusto: { type: Array, default: () => [] },
  contasContabeis: { type: Array, default: () => [] },
  fornecedores: { type: Array, default: () => [] },
})

const form = useForm({
  data: '',
  descricao: '',
  valor: 0,
  centro_custo_id: '',
  conta_contabil_id: '',
  fornecedor_id: '',
  documento: '',
  observacoes: '',
  ativo: true,
})

function submit() {
  form.post(route('despesas.store'))
}
</script>

<template>
  <Head title="Nova Despesa" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Nova Despesa</h1>
    <Link :href="route('despesas.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ExpenseForm
    :form="form"
    :centros-custo="props.centrosCusto"
    :contas-contabeis="props.contasContabeis"
    :fornecedores="props.fornecedores"
    submit-label="Criar Despesa"
    @submit="submit"
  />
</template>
