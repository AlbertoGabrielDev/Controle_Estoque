<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ClienteForm from './ClienteForm.vue'

const props = defineProps({
  segmentos: Array,
  ufs: Array,
  tabelasPreco: Array,
  impostos: Array,
})

const form = useForm({
  codigo: '',
  tipo_pessoa: 'PJ',
  documento: '',
  inscricao_estadual: '',
  razao_social: '',
  nome_fantasia: '',
  nif_cif: '',
  nome: '',
  email: '',
  whatsapp: '',
  telefone: '',
  site: '',
  cep: '',
  logradouro: '',
  numero: '',
  complemento: '',
  bairro: '',
  cidade: '',
  uf: '',
  pais: 'Brasil',
  endereco_faturacao: '',
  endereco_entrega: '',
  segment_id: '',
  limite_credito: '',
  bloqueado: false,
  tabela_preco_id: '',
  imposto_padrao_id: '',
  condicao_pagamento: '',
  ativo: true,
  observacoes: '',
})

function submit() {
  form.post(route('clientes.store'))
}
</script>

<template>
  <Head title="Novo Cliente" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Novo Cliente</h1>
    <Link :href="route('clientes.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ClienteForm
    :form="form"
    :ufs="ufs"
    :segmentos="segmentos"
    :tabelas-preco="tabelasPreco"
    :impostos="impostos"
    @submit="submit"
  />
</template>
