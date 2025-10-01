<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  cliente: Object,
  segmentos: Array,
  ufs: Array
})

const form = useForm({
  tipo_pessoa: props.cliente.tipo_pessoa ?? 'PJ',
  documento: props.cliente.documento ?? '',
  inscricao_estadual: props.cliente.inscricao_estadual ?? '',
  razao_social: props.cliente.razao_social ?? '',
  nome_fantasia: props.cliente.nome_fantasia ?? '',
  nome: props.cliente.nome ?? '',
  email: props.cliente.email ?? '',
  whatsapp: props.cliente.whatsapp ?? '',
  telefone: props.cliente.telefone ?? '',
  site: props.cliente.site ?? '',
  cep: props.cliente.cep ?? '',
  logradouro: props.cliente.logradouro ?? '',
  numero: props.cliente.numero ?? '',
  complemento: props.cliente.complemento ?? '',
  bairro: props.cliente.bairro ?? '',
  cidade: props.cliente.cidade ?? '',
  uf: props.cliente.uf ?? '',
  pais: props.cliente.pais ?? 'Brasil',
  segment_id: props.cliente.segment_id ?? '',
  limite_credito: props.cliente.limite_credito ?? '',
  bloqueado: !!props.cliente.bloqueado,
  tabela_preco: props.cliente.tabela_preco ?? '',
  status: props.cliente.status ?? 1,
  observacoes: props.cliente.observacoes ?? '',
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

  <!-- Reuso do formulário da Create com os mesmos campos -->
  <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded shadow">
    <!-- (os mesmos campos da Create.vue) -->
    <!-- por brevidade, você pode copiar os inputs exatamente iguais da Create.vue -->
    <!-- ... -->
    <div>
      <label class="block text-sm font-medium">Tipo de Pessoa</label>
      <select v-model="form.tipo_pessoa" class="mt-1 border rounded px-3 py-2 w-full">
        <option value="PF">PF</option>
        <option value="PJ">PJ</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Documento (CPF/CNPJ)</label>
      <input v-model="form.documento" class="mt-1 border rounded px-3 py-2 w-full" />
      <div v-if="form.errors.documento" class="text-red-600 text-sm">{{ form.errors.documento }}</div>
    </div>

    <div v-if="form.tipo_pessoa==='PJ'">
      <label class="block text-sm font-medium">Razão Social</label>
      <input v-model="form.razao_social" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div v-if="form.tipo_pessoa==='PJ'">
      <label class="block text-sm font-medium">Nome Fantasia</label>
      <input v-model="form.nome_fantasia" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div v-if="form.tipo_pessoa==='PF'">
      <label class="block text-sm font-medium">Nome</label>
      <input v-model="form.nome" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div>
      <label class="block text-sm font-medium">E-mail</label>
      <input v-model="form.email" type="email" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div>
      <label class="block text-sm font-medium">WhatsApp</label>
      <input v-model="form.whatsapp" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div>
      <label class="block text-sm font-medium">Telefone</label>
      <input v-model="form.telefone" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div class="md:col-span-2 pt-2 border-t">
      <h2 class="font-semibold mb-2">Endereço</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="block text-sm">CEP</label><input v-model="form.cep" class="mt-1 border rounded px-3 py-2 w-full" /></div>
        <div class="md:col-span-2"><label class="block text-sm">Logradouro</label><input v-model="form.logradouro" class="mt-1 border rounded px-3 py-2 w-full" /></div>
        <div><label class="block text-sm">Número</label><input v-model="form.numero" class="mt-1 border rounded px-3 py-2 w-full" /></div>
        <div><label class="block text-sm">Complemento</label><input v-model="form.complemento" class="mt-1 border rounded px-3 py-2 w-full" /></div>
        <div><label class="block text-sm">Bairro</label><input v-model="form.bairro" class="mt-1 border rounded px-3 py-2 w-full" /></div>
        <div><label class="block text-sm">Cidade</label><input v-model="form.cidade" class="mt-1 border rounded px-3 py-2 w-full" /></div>
        <div>
          <label class="block text-sm">UF</label>
          <select v-model="form.uf" class="mt-1 border rounded px-3 py-2 w-full">
            <option value="">—</option>
            <option v-for="u in ufs" :key="u" :value="u">{{ u }}</option>
          </select>
        </div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Segmento</label>
      <select v-model="form.segment_id" class="mt-1 border rounded px-3 py-2 w-full">
        <option value="">—</option>
        <option v-for="s in segmentos" :key="s.id" :value="s.id">{{ s.nome }}</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium">Limite de Crédito</label>
      <input v-model="form.limite_credito" type="number" step="0.01" min="0" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div>
      <label class="block text-sm font-medium">Tabela de Preço</label>
      <input v-model="form.tabela_preco" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div class="flex items-center gap-2">
      <input id="bloq" v-model="form.bloqueado" type="checkbox" />
      <label for="bloq">Bloqueado</label>
    </div>

    <div>
      <label class="block text-sm font-medium">Status</label>
      <select v-model="form.status" class="mt-1 border rounded px-3 py-2 w-full">
        <option :value="1">Ativo</option>
        <option :value="0">Inativo</option>
      </select>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Observações</label>
      <textarea v-model="form.observacoes" rows="3" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div class="md:col-span-2 flex justify-between">
      <Link :href="route('clientes.index')" class="px-3 py-2 rounded border">Voltar</Link>
      <div class="flex gap-2">
        <Link
          as="button"
          method="delete"
          :href="route('clientes.destroy', props.cliente.id_cliente)"
          class="px-3 py-2 rounded border border-red-600 text-red-600"
        >
          Excluir
        </Link>
        <button :disabled="form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">Salvar</button>
      </div>
    </div>
  </form>
</template>
