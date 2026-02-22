<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ImpostoForm from './Form.vue'

const form = useForm({
  codigo: '',
  nome: '',
  tipo: 'IVA',
  aliquota_percent: 0,
  ativo: true,
})

function submit() {
  form.post(route('impostos.store'))
}
</script>

<template>
  <Head title="Novo Imposto" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Novo Imposto</h1>
    <Link :href="route('impostos.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ImpostoForm :form="form" submit-label="Criar Imposto" @submit="submit" />
</template>
