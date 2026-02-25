<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import CategoryForm from './CategoryForm.vue'

const props = defineProps({
  categoriasPai: { type: Array, default: () => [] },
})

const form = useForm({
  codigo: '',
  nome_categoria: '',
  tipo: 'produto',
  categoria_pai_id: '',
  ativo: true,
  imagem: null,
})

function submit() {
  form.post(route('categoria.inserirCategoria'), {
    forceFormData: true,
  })
}
</script>

<template>
  <Head title="Cadastro de Categoria" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Cadastro de Categoria</h1>
    <Link :href="route('categoria.index')" class="text-blue-600">Voltar</Link>
  </div>

  <CategoryForm
    :form="form"
    :categorias-pai="props.categoriasPai"
    :show-image="true"
    submit-label="Criar Categoria"
    @submit="submit"
  />
</template>
