<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import PriceTableForm from './Form.vue'

const props = defineProps({
  tabela: { type: Object, required: true },
  itens: { type: Array, default: () => [] },
})

const form = useForm({
  codigo: props.tabela?.codigo ?? '',
  nome: props.tabela?.nome ?? '',
  moeda: props.tabela?.moeda ?? 'EUR',
  inicio_vigencia: props.tabela?.inicio_vigencia ?? '',
  fim_vigencia: props.tabela?.fim_vigencia ?? '',
  ativo: props.tabela?.ativo ?? true,
  itens: (props.tabela?.itens ?? []).map((i) => ({
    item_id: i.id,
    preco: i.pivot?.preco ?? 0,
    desconto_percent: i.pivot?.desconto_percent ?? 0,
    quantidade_minima: i.pivot?.quantidade_minima ?? 1,
  })),
})

function submit() {
  form.put(route('tabelas_preco.update', props.tabela.id))
}
</script>

<template>
  <Head title="Editar Tabela de Preço" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Tabela de Preço</h1>
    <Link :href="route('tabelas_preco.index')" class="text-blue-600">Voltar</Link>
  </div>

  <PriceTableForm :form="form" :itens="props.itens" submit-label="Salvar Alterações" @submit="submit" />
</template>
