<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import CategoryForm from './CategoryForm.vue'

const props = defineProps({
  categoria: { type: Object, required: true },
})

const form = useForm({
  nome_categoria: props.categoria?.nome_categoria ?? '',
})

function submit() {
  form.post(route('categorias.salvarEditar', props.categoria.id_categoria))
}
</script>

<template>
  <Head title="Editar Categoria" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Categoria</h1>
    <Link :href="route('categoria.index')" class="text-blue-600">Voltar</Link>
  </div>

  <CategoryForm :form="form" submit-label="Salvar Alterações" @submit="submit" />
</template>
