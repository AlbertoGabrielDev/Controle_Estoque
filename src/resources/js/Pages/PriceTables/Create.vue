<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import PriceTableForm from './Form.vue'

const props = defineProps({
  itens: { type: Array, default: () => [] },
})

const form = useForm({
  codigo: '',
  nome: '',
  moeda: 'EUR',
  inicio_vigencia: '',
  fim_vigencia: '',
  ativo: true,
  itens: [],
})

function submit() {
  form.post(route('tabelas_preco.store'))
}
</script>

<template>
  <Head title="Nova Tabela de Preço" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Nova Tabela de Preço</h1>
    <Link :href="route('tabelas_preco.index')" class="text-blue-600">Voltar</Link>
  </div>

  <PriceTableForm :form="form" :itens="props.itens" submit-label="Criar Tabela" @submit="submit" />
</template>
