<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import PriceTableForm from './Form.vue'

const props = defineProps({
  itens: { type: Array, default: () => [] },
  produtos: { type: Array, default: () => [] },
  marcas: { type: Array, default: () => [] },
  fornecedores: { type: Array, default: () => [] },
  marcasPorProduto: { type: Object, default: () => ({}) },
  fornecedoresPorProduto: { type: Object, default: () => ({}) },
})

const form = useForm({
  codigo: '',
  nome: '',
  tipo_alvo: 'item',
  moeda: 'EUR',
  inicio_vigencia: '',
  fim_vigencia: '',
  ativo: true,
  itens: [],
})

function submit() {
  form.transform((data) => ({
    ...data,
    itens: (data.itens || [])
      .map((row) => {
        if (data.tipo_alvo === 'produto') {
          return {
            item_id: null,
            produto_id: row.item_id ? Number(row.item_id) : null,
            marca_id: row.marca_id ? Number(row.marca_id) : null,
            fornecedor_id: row.fornecedor_id ? Number(row.fornecedor_id) : null,
            preco: row.preco,
            desconto_percent: row.desconto_percent,
            quantidade_minima: row.quantidade_minima,
          }
        }

        return {
          item_id: row.item_id ? Number(row.item_id) : null,
          produto_id: null,
          marca_id: null,
          fornecedor_id: null,
          preco: row.preco,
          desconto_percent: row.desconto_percent,
          quantidade_minima: row.quantidade_minima,
        }
      })
      .filter((row) => row.item_id || row.produto_id),
  }))
  form.post(route('tabelas_preco.store'))
}
</script>

<template>
  <Head title="Nova Tabela de Preço" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Nova Tabela de Preço</h1>
    <Link :href="route('tabelas_preco.index')" class="text-blue-600">Voltar</Link>
  </div>

  <PriceTableForm
    :form="form"
    :itens="props.itens"
    :produtos="props.produtos"
    :marcas="props.marcas"
    :fornecedores="props.fornecedores"
    :marcas-por-produto="props.marcasPorProduto"
    :fornecedores-por-produto="props.fornecedoresPorProduto"
    submit-label="Criar Tabela"
    @submit="submit"
  />
</template>
