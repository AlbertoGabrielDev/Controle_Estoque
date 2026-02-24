<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import PriceTableForm from './Form.vue'

const props = defineProps({
  tabela: { type: Object, required: true },
  itens: { type: Array, default: () => [] },
  produtos: { type: Array, default: () => [] },
  marcasPorProduto: { type: Object, default: () => ({}) },
  fornecedoresPorProduto: { type: Object, default: () => ({}) },
})

const form = useForm({
  codigo: props.tabela?.codigo ?? '',
  nome: props.tabela?.nome ?? '',
  tipo_alvo: props.tabela?.tipo_alvo ?? 'item',
  moeda: props.tabela?.moeda ?? 'EUR',
  inicio_vigencia: props.tabela?.inicio_vigencia ?? '',
  fim_vigencia: props.tabela?.fim_vigencia ?? '',
  ativo: props.tabela?.ativo ?? true,
  itens:
    (props.tabela?.tipo_alvo ?? 'item') === 'produto'
      ? (props.tabela?.produtos ?? []).map((p) => ({
          item_id: p.id_produto,
          marca_id: p.pivot?.marca_id ?? '',
          fornecedor_id: p.pivot?.fornecedor_id ?? '',
          preco: p.pivot?.preco ?? 0,
          desconto_percent: p.pivot?.desconto_percent ?? 0,
          quantidade_minima: p.pivot?.quantidade_minima ?? 1,
        }))
      : (props.tabela?.itens ?? []).map((i) => ({
          item_id: i.id,
          marca_id: '',
          fornecedor_id: '',
          preco: i.pivot?.preco ?? 0,
          desconto_percent: i.pivot?.desconto_percent ?? 0,
          quantidade_minima: i.pivot?.quantidade_minima ?? 1,
        })),
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
  form.put(route('tabelas_preco.update', props.tabela.id))
}
</script>

<template>
  <Head title="Editar Tabela de Preço" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Tabela de Preço</h1>
    <Link :href="route('tabelas_preco.index')" class="text-blue-600">Voltar</Link>
  </div>

  <PriceTableForm
    :form="form"
    :itens="props.itens"
    :produtos="props.produtos"
    :marcas-por-produto="props.marcasPorProduto"
    :fornecedores-por-produto="props.fornecedoresPorProduto"
    submit-label="Salvar Alterações"
    @submit="submit"
  />
</template>

