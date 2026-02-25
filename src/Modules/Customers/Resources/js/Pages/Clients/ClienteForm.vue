<script setup>
import { watch } from 'vue'

const props = defineProps({
  form:      { type: Object, required: true },  // vem do pai (Inertia useForm)
  ufs:       { type: Array,  default: () => [] },
  segmentos: { type: Array,  default: () => [] },
  tabelasPreco: { type: Array, default: () => [] },
  impostos: { type: Array, default: () => [] },
})

const emit = defineEmits(['submit'])

// Limpa campos conforme tipo de pessoa
watch(
  () => props.form?.tipo_pessoa,
  (novo) => {
    if (!props.form) return
    if (novo === 'PF') {
      props.form.razao_social = ''
      props.form.nome_fantasia = ''
      props.form.inscricao_estadual = ''
    } else if (novo === 'PJ') {
      props.form.nome = ''
    }
  },
  { immediate: true }
)
</script>

<template>
  <form @submit.prevent="emit('submit')" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded shadow">
    <div>
      <label class="block text-sm font-medium">Código</label>
      <input v-model="form.codigo" class="mt-1 border rounded px-3 py-2 w-full" />
      <div v-if="form.errors?.codigo" class="text-red-600 text-sm">{{ form.errors.codigo }}</div>
    </div>

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
      <div v-if="form.errors?.documento" class="text-red-600 text-sm">{{ form.errors.documento }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium">NIF/CIF</label>
      <input v-model="form.nif_cif" class="mt-1 border rounded px-3 py-2 w-full" />
      <div v-if="form.errors?.nif_cif" class="text-red-600 text-sm">{{ form.errors.nif_cif }}</div>
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
      <label class="block text-sm font-medium">Tabela de Preço</label>
      <select v-model="form.tabela_preco_id" class="mt-1 border rounded px-3 py-2 w-full">
        <option value="">—</option>
        <option v-for="t in tabelasPreco" :key="t.id" :value="t.id">{{ t.codigo }} - {{ t.nome }}</option>
      </select>
      <div v-if="form.errors?.tabela_preco_id" class="text-red-600 text-sm">{{ form.errors.tabela_preco_id }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium">Imposto Padrão</label>
      <select v-model="form.imposto_padrao_id" class="mt-1 border rounded px-3 py-2 w-full">
        <option value="">—</option>
        <option v-for="i in impostos" :key="i.id" :value="i.id">{{ i.codigo }} - {{ i.nome }}</option>
      </select>
      <div v-if="form.errors?.imposto_padrao_id" class="text-red-600 text-sm">{{ form.errors.imposto_padrao_id }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium">Limite de Crédito</label>
      <input v-model="form.limite_credito" type="number" step="0.01" min="0" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div>
      <label class="block text-sm font-medium">Condição de Pagamento</label>
      <input v-model="form.condicao_pagamento" class="mt-1 border rounded px-3 py-2 w-full" />
      <div v-if="form.errors?.condicao_pagamento" class="text-red-600 text-sm">{{ form.errors.condicao_pagamento }}</div>
    </div>

    <div class="flex items-center gap-2">
      <input id="bloq" v-model="form.bloqueado" type="checkbox" />
      <label for="bloq">Bloqueado</label>
    </div>

    <div>
      <label class="block text-sm font-medium">Ativo</label>
      <select v-model="form.ativo" class="mt-1 border rounded px-3 py-2 w-full">
        <option :value="true">Ativo</option>
        <option :value="false">Inativo</option>
      </select>
      <div v-if="form.errors?.ativo" class="text-red-600 text-sm">{{ form.errors.ativo }}</div>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Endereço de Faturação</label>
      <textarea v-model="form.endereco_faturacao" rows="2" class="mt-1 border rounded px-3 py-2 w-full" />
      <div v-if="form.errors?.endereco_faturacao" class="text-red-600 text-sm">{{ form.errors.endereco_faturacao }}</div>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Endereço de Entrega</label>
      <textarea v-model="form.endereco_entrega" rows="2" class="mt-1 border rounded px-3 py-2 w-full" />
      <div v-if="form.errors?.endereco_entrega" class="text-red-600 text-sm">{{ form.errors.endereco_entrega }}</div>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Observações</label>
      <textarea v-model="form.observacoes" rows="3" class="mt-1 border rounded px-3 py-2 w-full" />
    </div>

    <div class="md:col-span-2 flex justify-end gap-2">
      <button class="px-3 py-2 rounded bg-blue-600 text-white" :disabled="form.processing">Salvar</button>
    </div>
  </form>
</template>
