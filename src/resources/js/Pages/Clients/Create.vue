<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ClienteForm from './ClienteForm.vue'

const props = defineProps({
  segmentos: Array,
  ufs: Array
})

const form = useForm({
  tipo_pessoa: 'PJ',
  documento: '',
  inscricao_estadual: '',
  razao_social: '',
  nome_fantasia: '',
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
  segment_id: '',
  limite_credito: '',
  bloqueado: false,
  tabela_preco: '',
  status: 1,
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

  <ClienteForm :form="form" :ufs="ufs" :segmentos="segmentos" @submit="submit" />
</template>
