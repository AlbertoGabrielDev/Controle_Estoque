<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ClienteForm from './ClienteForm.vue'

const props = defineProps({
  cliente: Object,
  segmentos: Array,
  ufs: Array,
  tabelasPreco: Array,
  impostos: Array,
})

const form = useForm({
  codigo: props.cliente?.codigo ?? '',
  tipo_pessoa: props.cliente?.tipo_pessoa ?? 'PJ',
  documento: props.cliente?.documento ?? '',
  inscricao_estadual: props.cliente?.inscricao_estadual ?? '',
  razao_social: props.cliente?.razao_social ?? '',
  nome_fantasia: props.cliente?.nome_fantasia ?? '',
  nif_cif: props.cliente?.nif_cif ?? '',
  nome: props.cliente?.nome ?? '',
  email: props.cliente?.email ?? '',
  whatsapp: props.cliente?.whatsapp ?? '',
  telefone: props.cliente?.telefone ?? '',
  site: props.cliente?.site ?? '',
  cep: props.cliente?.cep ?? '',
  logradouro: props.cliente?.logradouro ?? '',
  numero: props.cliente?.numero ?? '',
  complemento: props.cliente?.complemento ?? '',
  bairro: props.cliente?.bairro ?? '',
  cidade: props.cliente?.cidade ?? '',
  uf: props.cliente?.uf ?? '',
  pais: props.cliente?.pais ?? 'Brasil',
  endereco_faturacao: props.cliente?.endereco_faturacao ?? '',
  endereco_entrega: props.cliente?.endereco_entrega ?? '',
  segment_id: props.cliente?.segment_id ?? '',
  limite_credito: props.cliente?.limite_credito ?? '',
  bloqueado: !!props.cliente?.bloqueado,
  tabela_preco_id: props.cliente?.tabela_preco_id ?? '',
  imposto_padrao_id: props.cliente?.imposto_padrao_id ?? '',
  condicao_pagamento: props.cliente?.condicao_pagamento ?? '',
  ativo: props.cliente?.ativo ?? true,
  observacoes: props.cliente?.observacoes ?? '',
})

function submit() {
  form.put(route('clientes.update', props.cliente.id_cliente))
}
</script>

<template>
  <Head title="Editar Cliente" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Cliente</h1>
    <Link :href="route('clientes.show', props.cliente.id_cliente)" class="text-blue-600">Ver</Link>
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
