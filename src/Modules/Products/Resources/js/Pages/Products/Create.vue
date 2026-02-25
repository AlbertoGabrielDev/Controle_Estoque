<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ProductForm from './ProductForm.vue'

const props = defineProps({
  categorias: { type: Array, default: () => [] },
  unidades: { type: Array, default: () => [] },
  itens: { type: Array, default: () => [] },
})

const form = useForm({
  cod_produto: '',
  nome_produto: '',
  descricao: '',
  unidade_medida_id: '',
  item_id: '',
  inf_nutriente: '',
  id_categoria_fk: '',
})

function submit() {
  form.post(route('produtos.salvarCadastro'))
}
</script>

<template>
  <Head title="Cadastro de Produto" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Cadastro de Produto</h1>
    <Link :href="route('produtos.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ProductForm
    :form="form"
    :categorias="props.categorias"
    :unidades="props.unidades"
    :itens="props.itens"
    submit-label="Criar Produto"
    @submit="submit"
  />
</template>
