<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ProductForm from './ProductForm.vue'

const props = defineProps({
  produto: { type: Object, required: true },
  categorias: { type: Array, default: () => [] },
  categoriaSelecionada: { type: [String, Number, null], default: null },
})

function stringifyNutrition(value) {
  if (value == null || value === '') return ''
  if (typeof value === 'string') return value
  try {
    return JSON.stringify(value, null, 2)
  } catch {
    return String(value)
  }
}

const form = useForm({
  cod_produto: props.produto?.cod_produto ?? '',
  nome_produto: props.produto?.nome_produto ?? '',
  descricao: props.produto?.descricao ?? '',
  unidade_medida: props.produto?.unidade_medida ?? '',
  qrcode: props.produto?.qrcode ?? '',
  inf_nutriente: stringifyNutrition(props.produto?.inf_nutriente),
  id_categoria_fk: props.categoriaSelecionada ?? '',
})

function submit() {
  form.post(route('produtos.salvarEditar', props.produto.id_produto))
}
</script>

<template>
  <Head title="Editar Produto" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Produto</h1>
    <Link :href="route('produtos.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ProductForm
    :form="form"
    :categorias="props.categorias"
    :show-qrcode="true"
    submit-label="Salvar Alterações"
    @submit="submit"
  />
</template>
