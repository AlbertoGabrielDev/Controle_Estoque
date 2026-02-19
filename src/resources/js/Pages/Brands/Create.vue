<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import BrandForm from './BrandForm.vue'

const form = useForm({
  nome_marca: '',
})

function submit() {
  form.post(route('marca.inserirMarca'))
}
</script>

<template>
  <Head title="Cadastro de Marca" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Cadastro de Marca</h1>
    <Link :href="route('marca.index')" class="text-blue-600">Voltar</Link>
  </div>

  <BrandForm :form="form" submit-label="Criar Marca" @submit="submit" />
</template>
