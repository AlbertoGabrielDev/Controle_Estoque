<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import StockForm from './StockForm.vue'

const props = defineProps({
  estoque: { type: Object, required: true },
  fornecedores: { type: Array, default: () => [] },
  marcas: { type: Array, default: () => [] },
  produtos: { type: Array, default: () => [] },
  previewVm: { type: Object, default: null },
  rawImpostos: { type: Object, default: () => ({}) },
})

const form = useForm({
  id_produto_fk: props.estoque?.id_produto_fk ?? '',
  id_fornecedor_fk: props.estoque?.id_fornecedor_fk ?? '',
  id_marca_fk: props.estoque?.id_marca_fk ?? '',
  quantidade: props.estoque?.quantidade ?? '',
  preco_custo: props.estoque?.preco_custo ?? '',
  preco_venda: props.estoque?.preco_venda ?? '',
  quantidade_aviso: props.estoque?.quantidade_aviso ?? '',
  lote: props.estoque?.lote ?? '',
  localizacao: props.estoque?.localizacao ?? '',
  validade: props.estoque?.validade?.slice?.(0, 10) ?? '',
  data_chegada: props.estoque?.data_chegada?.slice?.(0, 10) ?? '',
  imposto_total: props.estoque?.imposto_total ?? props.previewVm?.__totais?.total_impostos ?? 0,
  impostos_json: props.estoque?.impostos_json ?? JSON.stringify(props.rawImpostos?._compact ?? props.rawImpostos ?? {}),
  id_tax_fk: props.estoque?.id_tax_fk ?? null,
})

function submit() {
  form.put(route('estoque.salvarEditar', props.estoque.id_estoque))
}
</script>

<template>
  <Head title="Editar Estoque" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Estoque</h1>
    <Link :href="route('estoque.index')" class="text-blue-600">Voltar</Link>
  </div>

  <StockForm
    :form="form"
    :fornecedores="props.fornecedores"
    :marcas="props.marcas"
    :produtos="props.produtos"
    :lock-product="true"
    :product-label="props.estoque?.produtos?.nome_produto || ''"
    :initial-vm="props.previewVm"
    submit-label="Salvar Alterações"
    @submit="submit"
  />
</template>
