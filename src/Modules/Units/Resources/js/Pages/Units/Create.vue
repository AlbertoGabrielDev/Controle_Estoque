<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import UnitForm from './UnitForm.vue'

const form = useForm({
  nome: '',
})

function submit() {
  form.post(route('unidades.inserirUnidade'))
}
</script>

<template>
  <Head title="Cadastro de Unidade" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Cadastro de Unidade</h1>
    <Link :href="route('unidade.index')" class="text-blue-600">Voltar</Link>
  </div>

  <UnitForm :form="form" submit-label="Criar Unidade" @submit="submit" />
</template>
