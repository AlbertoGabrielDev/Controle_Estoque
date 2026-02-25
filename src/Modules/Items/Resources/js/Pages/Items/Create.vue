<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ItemForm from './Form.vue'

const props = defineProps({
  categorias: { type: Array, default: () => [] },
  unidades: { type: Array, default: () => [] },
})

const form = useForm({
  sku: '',
  nome: '',
  tipo: 'produto',
  categoria_id: '',
  unidade_medida_id: '',
  descricao: '',
  custo: 0,
  preco_base: 0,
  peso_kg: '',
  volume_m3: '',
  controla_estoque: true,
  ativo: true,
})

function submit() {
  form.post(route('itens.store'))
}
</script>

<template>
  <Head title="Novo Item" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Novo Item</h1>
    <Link :href="route('itens.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ItemForm :form="form" :categorias="props.categorias" :unidades="props.unidades" submit-label="Criar Item" @submit="submit" />
</template>
