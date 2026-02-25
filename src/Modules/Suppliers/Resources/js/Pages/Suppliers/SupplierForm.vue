<script setup>
const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Salvar' },
})

defineEmits(['submit'])
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow max-w-5xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Código</label>
        <input v-model="props.form.codigo" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.codigo" class="text-red-600 text-sm mt-1">{{ props.form.errors.codigo }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Razão Social</label>
        <input v-model="props.form.razao_social" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.razao_social" class="text-red-600 text-sm mt-1">{{ props.form.errors.razao_social }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Nome do Fornecedor</label>
        <input
          v-model="props.form.nome_fornecedor"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.nome_fornecedor" class="text-red-600 text-sm mt-1">{{ props.form.errors.nome_fornecedor }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">CNPJ</label>
        <input
          v-model="props.form.cnpj"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.cnpj" class="text-red-600 text-sm mt-1">{{ props.form.errors.cnpj }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">NIF/CIF</label>
        <input
          v-model="props.form.nif_cif"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.nif_cif" class="text-red-600 text-sm mt-1">{{ props.form.errors.nif_cif }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">CEP</label>
        <input
          v-model="props.form.cep"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.cep" class="text-red-600 text-sm mt-1">{{ props.form.errors.cep }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Logradouro</label>
        <input
          v-model="props.form.logradouro"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.logradouro" class="text-red-600 text-sm mt-1">{{ props.form.errors.logradouro }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Bairro</label>
        <input
          v-model="props.form.bairro"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.bairro" class="text-red-600 text-sm mt-1">{{ props.form.errors.bairro }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Numero</label>
        <input
          v-model="props.form.numero_casa"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.numero_casa" class="text-red-600 text-sm mt-1">{{ props.form.errors.numero_casa }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Email</label>
        <input
          v-model="props.form.email"
          type="email"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.email" class="text-red-600 text-sm mt-1">{{ props.form.errors.email }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">DDD</label>
        <input
          v-model="props.form.ddd"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.ddd" class="text-red-600 text-sm mt-1">{{ props.form.errors.ddd }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Cidade</label>
        <input
          v-model="props.form.cidade"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.cidade" class="text-red-600 text-sm mt-1">{{ props.form.errors.cidade }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">UF</label>
        <input
          v-model="props.form.uf"
          class="mt-1 border rounded px-3 py-2 w-full uppercase"
        >
        <div v-if="props.form.errors.uf" class="text-red-600 text-sm mt-1">{{ props.form.errors.uf }}</div>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium">Endereço (Texto)</label>
        <textarea v-model="props.form.endereco" rows="2" class="mt-1 border rounded px-3 py-2 w-full" />
        <div v-if="props.form.errors.endereco" class="text-red-600 text-sm mt-1">{{ props.form.errors.endereco }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Prazo de Entrega (dias)</label>
        <input v-model="props.form.prazo_entrega_dias" type="number" min="0" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.prazo_entrega_dias" class="text-red-600 text-sm mt-1">{{ props.form.errors.prazo_entrega_dias }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Condição de Pagamento</label>
        <input v-model="props.form.condicao_pagamento" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.condicao_pagamento" class="text-red-600 text-sm mt-1">{{ props.form.errors.condicao_pagamento }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Telefone</label>
        <input
          v-model="props.form.telefone"
          class="mt-1 border rounded px-3 py-2 w-full"
        >
        <div v-if="props.form.errors.telefone" class="text-red-600 text-sm mt-1">{{ props.form.errors.telefone }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Ativo</label>
        <select v-model="props.form.ativo" class="mt-1 border rounded px-3 py-2 w-full">
          <option :value="true">Ativo</option>
          <option :value="false">Inativo</option>
        </select>
        <div v-if="props.form.errors.ativo" class="text-red-600 text-sm mt-1">{{ props.form.errors.ativo }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
      <label class="flex items-center gap-2 border rounded px-3 py-2">
        <input v-model="props.form.principal" type="checkbox">
        <span>Principal</span>
      </label>
      <label class="flex items-center gap-2 border rounded px-3 py-2">
        <input v-model="props.form.whatsapp" type="checkbox">
        <span>Whatsapp</span>
      </label>
      <label class="flex items-center gap-2 border rounded px-3 py-2">
        <input v-model="props.form.telegram" type="checkbox">
        <span>Telegram</span>
      </label>
    </div>

    <div class="mt-4 flex justify-end gap-2">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">{{ submitLabel }}</button>
    </div>
  </form>
</template>
