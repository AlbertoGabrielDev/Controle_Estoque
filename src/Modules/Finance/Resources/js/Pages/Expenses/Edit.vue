<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ExpenseForm from './Form.vue'

const props = defineProps({
  despesa: { type: Object, required: true },
  centrosCusto: { type: Array, default: () => [] },
  contasContabeis: { type: Array, default: () => [] },
  fornecedores: { type: Array, default: () => [] },
})

const form = useForm({
  data: props.despesa?.data ? String(props.despesa.data).slice(0, 10) : '',
  descricao: props.despesa?.descricao ?? '',
  valor: props.despesa?.valor ?? 0,
  centro_custo_id: props.despesa?.centro_custo_id ?? '',
  conta_contabil_id: props.despesa?.conta_contabil_id ?? '',
  fornecedor_id: props.despesa?.fornecedor_id ?? '',
  documento: props.despesa?.documento ?? '',
  observacoes: props.despesa?.observacoes ?? '',
  ativo: props.despesa?.ativo ?? true,
})

function submit() {
  form.put(route('despesas.update', props.despesa.id))
}
</script>

<template>
  <Head title="Editar Despesa" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Despesa</h1>
    <Link :href="route('despesas.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ExpenseForm
    :form="form"
    :centros-custo="props.centrosCusto"
    :contas-contabeis="props.contasContabeis"
    :fornecedores="props.fornecedores"
    submit-label="Salvar Alterações"
    @submit="submit"
  />
</template>
