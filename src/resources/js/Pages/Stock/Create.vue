<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import StockForm from './StockForm.vue'

const props = defineProps({
  fornecedores: { type: Array, default: () => [] },
  marcas: { type: Array, default: () => [] },
  produtos: { type: Array, default: () => [] },
})

const form = useForm({
  id_produto_fk: '',
  id_fornecedor_fk: '',
  id_marca_fk: '',
  quantidade: '',
  preco_custo: '',
  preco_venda: '',
  quantidade_aviso: '',
  lote: '',
  localizacao: '',
  validade: '',
  data_chegada: '',
  imposto_total: null,
  impostos_json: '',
  id_tax_fk: null,
})

function submit() {
  form.post(route('estoque.inserirEstoque'))
}
</script>

<template>
  <Head title="Cadastro de Estoque" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Cadastro de Estoque</h1>
    <Link :href="route('estoque.index')" class="text-blue-600">Voltar</Link>
  </div>

  <StockForm
    :form="form"
    :fornecedores="props.fornecedores"
    :marcas="props.marcas"
    :produtos="props.produtos"
    submit-label="Criar Estoque"
    @submit="submit"
  />
</template>
