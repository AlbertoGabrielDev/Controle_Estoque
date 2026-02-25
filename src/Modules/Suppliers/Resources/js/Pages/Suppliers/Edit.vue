<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import SupplierForm from './SupplierForm.vue'

const props = defineProps({
  fornecedor: { type: Object, required: true },
  telefone: { type: Object, default: null },
})

const form = useForm({
  codigo: props.fornecedor?.codigo ?? '',
  razao_social: props.fornecedor?.razao_social ?? '',
  nome_fornecedor: props.fornecedor?.nome_fornecedor ?? '',
  cnpj: props.fornecedor?.cnpj ?? '',
  nif_cif: props.fornecedor?.nif_cif ?? '',
  cep: props.fornecedor?.cep ?? '',
  logradouro: props.fornecedor?.logradouro ?? '',
  bairro: props.fornecedor?.bairro ?? '',
  numero_casa: props.fornecedor?.numero_casa ?? '',
  email: props.fornecedor?.email ?? '',
  cidade: props.fornecedor?.cidade ?? '',
  uf: props.fornecedor?.uf ?? '',
  endereco: props.fornecedor?.endereco ?? '',
  prazo_entrega_dias: props.fornecedor?.prazo_entrega_dias ?? 0,
  condicao_pagamento: props.fornecedor?.condicao_pagamento ?? '',
  ddd: props.telefone?.ddd ?? '',
  telefone: props.telefone?.telefone ?? '',
  principal: Number(props.telefone?.principal ?? 0) === 1,
  whatsapp: Number(props.telefone?.whatsapp ?? 0) === 1,
  telegram: Number(props.telefone?.telegram ?? 0) === 1,
  ativo: props.fornecedor?.ativo ?? true,
})

function submit() {
  form.post(route('fornecedor.salvarEditar', props.fornecedor.id_fornecedor))
}
</script>

<template>
  <Head title="Editar Fornecedor" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Fornecedor</h1>
    <Link :href="route('fornecedor.index')" class="text-blue-600">Voltar</Link>
  </div>

  <SupplierForm :form="form" submit-label="Salvar Alteracoes" @submit="submit" />
</template>
