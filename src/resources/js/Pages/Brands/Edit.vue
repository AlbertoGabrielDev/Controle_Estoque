<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import BrandForm from './BrandForm.vue'

const props = defineProps({
  marca: { type: Object, required: true },
})

const form = useForm({
  nome_marca: props.marca?.nome_marca ?? '',
})

function submit() {
  form.post(route('marca.salvarEditar', props.marca.id_marca))
}
</script>

<template>
  <Head title="Editar Marca" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Marca</h1>
    <Link :href="route('marca.index')" class="text-blue-600">Voltar</Link>
  </div>

  <BrandForm :form="form" submit-label="Salvar Alterações" @submit="submit" />
</template>
