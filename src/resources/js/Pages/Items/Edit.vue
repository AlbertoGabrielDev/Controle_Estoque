<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ItemForm from './Form.vue'

const props = defineProps({
  item: { type: Object, required: true },
  categorias: { type: Array, default: () => [] },
  unidades: { type: Array, default: () => [] },
})

const form = useForm({
  sku: props.item?.sku ?? '',
  nome: props.item?.nome ?? '',
  tipo: props.item?.tipo ?? 'produto',
  categoria_id: props.item?.categoria_id ?? '',
  unidade_medida_id: props.item?.unidade_medida_id ?? '',
  descricao: props.item?.descricao ?? '',
  custo: props.item?.custo ?? 0,
  preco_base: props.item?.preco_base ?? 0,
  peso_kg: props.item?.peso_kg ?? '',
  volume_m3: props.item?.volume_m3 ?? '',
  controla_estoque: props.item?.controla_estoque ?? true,
  ativo: props.item?.ativo ?? true,
})

function submit() {
  form.put(route('itens.update', props.item.id))
}
</script>

<template>
  <Head title="Editar Item" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Item</h1>
    <Link :href="route('itens.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ItemForm :form="form" :categorias="props.categorias" :unidades="props.unidades" submit-label="Salvar Alterações" @submit="submit" />
</template>
