<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  cliente: Object,
  metricas: Object,
  tab: String
})
</script>

<template>
  <Head :title="`Cliente: ${cliente.nome_fantasia || cliente.razao_social || cliente.nome || cliente.id_cliente}`" />

  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-semibold">
        {{ cliente.nome_fantasia || cliente.razao_social || cliente.nome || 'Cliente' }}
      </h1>
      <div class="text-gray-500 text-sm">
        {{ cliente.documento || '—' }} • {{ cliente.whatsapp || '—' }} • {{ cliente.email || '—' }}
      </div>
    </div>
    <div class="flex gap-2">
      <Link :href="route('clientes.edit', cliente.id_cliente)" class="px-3 py-2 rounded bg-gray-100">Editar</Link>
      <Link :href="route('clientes.index')" class="px-3 py-2 rounded border">Voltar</Link>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded shadow p-4">
      <div class="text-gray-500 text-sm">Segmento</div>
      <div class="text-lg">{{ cliente.segmento?.nome || '—' }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
      <div class="text-gray-500 text-sm">Pedidos</div>
      <div class="text-lg">{{ metricas.pedidos_total }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
      <div class="text-gray-500 text-sm">Carrinhos abertos</div>
      <div class="text-lg">{{ metricas.carrinhos_abertos }}</div>
    </div>
  </div>

  <!-- Abas -->
  <div class="mb-2 flex gap-2">
    <Link :href="route('clientes.show', { cliente: cliente.id_cliente, tab: 'resumo' })" class="px-3 py-2 rounded border"
      :class="{ 'bg-blue-600 text-white border-blue-600': tab==='resumo' }"
    >Resumo</Link>
    <Link :href="route('clientes.show', { cliente: cliente.id_cliente, tab: 'pedidos' })" class="px-3 py-2 rounded border"
      :class="{ 'bg-blue-600 text-white border-blue-600': tab==='pedidos' }"
    >Pedidos</Link>
    <Link :href="route('clientes.show', { cliente: cliente.id_cliente, tab: 'carrinhos' })" class="px-3 py-2 rounded border"
      :class="{ 'bg-blue-600 text-white border-blue-600': tab==='carrinhos' }"
    >Carrinhos</Link>
    <Link :href="route('clientes.show', { cliente: cliente.id_cliente, tab: 'fiscal' })" class="px-3 py-2 rounded border"
      :class="{ 'bg-blue-600 text-white border-blue-600': tab==='fiscal' }"
    >Fiscal/Comercial</Link>

  </div>

  <div class="bg-white rounded shadow p-4">
    <div v-if="tab==='resumo'">
      <h2 class="font-semibold mb-2">Dados</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <div><b>Endereço:</b> {{ cliente.logradouro }} {{ cliente.numero }}, {{ cliente.bairro }} - {{ cliente.cidade }}/{{ cliente.uf }} • {{ cliente.cep }}</div>
        <div><b>Limite de crédito:</b> {{ cliente.limite_credito ?? '—' }}</div>
        <div><b>Tabela preço:</b> {{ cliente.tabela_preco ?? '—' }}</div>
        <div><b>Status:</b> {{ cliente.status ? 'Ativo' : 'Inativo' }} <span v-if="cliente.bloqueado" class="text-red-600">(Bloqueado)</span></div>
      </div>
      <div class="mt-4">
        <b>Observações:</b>
        <div class="text-gray-700 whitespace-pre-line">{{ cliente.observacoes || '—' }}</div>
      </div>
    </div>

    <div v-else-if="tab==='pedidos'">
      <!-- Aqui renderize sua lista de pedidos desse cliente (chame um endpoint se quiser) -->
      <div class="text-gray-500">Em breve: histórico de pedidos…</div>
    </div>

    <div v-else-if="tab==='carrinhos'">
      <div class="text-gray-500">Em breve: carrinhos em aberto…</div>
    </div>

    <div v-else-if="tab==='fiscal'">
      <div class="text-gray-500">Em breve: dados fiscais/comerciais adicionais…</div>
    </div>


  </div>
</template>

