<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import SupplierForm from './SupplierForm.vue'

const form = useForm({
  nome_fornecedor: '',
  cnpj: '',
  cep: '',
  logradouro: '',
  bairro: '',
  numero_casa: '',
  email: '',
  cidade: '',
  uf: '',
  ddd: '',
  telefone: '',
  principal: false,
  whatsapp: false,
  telegram: false,
})

function submit() {
  form.post(route('fornecedor.inserirCadastro'))
}
</script>

<template>
  <Head title="Cadastro de Fornecedor" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Cadastro de Fornecedor</h1>
    <Link :href="route('fornecedor.index')" class="text-blue-600">Voltar</Link>
  </div>

  <SupplierForm :form="form" submit-label="Cadastrar Fornecedor" @submit="submit" />
</template>

