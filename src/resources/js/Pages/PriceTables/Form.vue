<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  itens: { type: Array, default: () => [] },
  produtos: { type: Array, default: () => [] },
  marcas: { type: Array, default: () => [] },
  fornecedores: { type: Array, default: () => [] },
  marcasPorProduto: { type: Object, default: () => ({}) },
  fornecedoresPorProduto: { type: Object, default: () => ({}) },
  submitLabel: { type: String, default: 'Salvar' },
})

defineEmits(['submit'])

const options = computed(() => {
  if (props.form.tipo_alvo === 'produto') {
    return (props.produtos ?? []).map((p) => ({
      id: p.id_produto,
      label: `${p.cod_produto} - ${p.nome_produto}`,
    }))
  }

  return (props.itens ?? []).map((i) => ({
    id: i.id,
    label: `${i.sku} - ${i.nome}`,
  }))
})

const filteredOptions = () => options.value

function resolveProductKey(value) {
  if (value === null || value === undefined || value === '') {
    return ''
  }
  return String(value)
}

function marcasForRow(row) {
  if (!row?.item_id) {
    return []
  }
  const key = resolveProductKey(row.item_id)
  const list = props.marcasPorProduto?.[key] ?? []
  if (!row.marca_id) {
    return list
  }
  const selectedId = Number(row.marca_id)
  if (list.some((item) => Number(item.id_marca) === selectedId)) {
    return list
  }
  const fallback = (props.marcas ?? []).find((item) => Number(item.id_marca) === selectedId)
  return fallback ? [...list, fallback] : list
}

function fornecedoresForRow(row) {
  if (!row?.item_id) {
    return []
  }
  const key = resolveProductKey(row.item_id)
  const list = props.fornecedoresPorProduto?.[key] ?? []
  if (!row.fornecedor_id) {
    return list
  }
  const selectedId = Number(row.fornecedor_id)
  if (list.some((item) => Number(item.id_fornecedor) === selectedId)) {
    return list
  }
  const fallback = (props.fornecedores ?? []).find((item) => Number(item.id_fornecedor) === selectedId)
  return fallback ? [...list, fallback] : list
}

function onProdutoChange(row) {
  if (!row) return
  row.marca_id = ''
  row.fornecedor_id = ''
}

function addItem() {
  props.form.itens.push({
    item_id: '',
    marca_id: '',
    fornecedor_id: '',
    preco: 0,
    desconto_percent: 0,
    quantidade_minima: 1,
  })
}

function removeItem(index) {
  props.form.itens.splice(index, 1)
}

watch(
  () => props.form.tipo_alvo,
  (novo, antigo) => {
    if (antigo && novo !== antigo) {
      props.form.itens = []
    }
  }
)
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Código</label>
        <input v-model="props.form.codigo" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.codigo" class="text-red-600 text-sm mt-1">{{ props.form.errors.codigo }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Nome</label>
        <input v-model="props.form.nome" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.nome" class="text-red-600 text-sm mt-1">{{ props.form.errors.nome }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Moeda</label>
        <input v-model="props.form.moeda" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.moeda" class="text-red-600 text-sm mt-1">{{ props.form.errors.moeda }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Início Vigência</label>
        <input v-model="props.form.inicio_vigencia" type="date" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.inicio_vigencia" class="text-red-600 text-sm mt-1">{{ props.form.errors.inicio_vigencia }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Fim Vigência</label>
        <input v-model="props.form.fim_vigencia" type="date" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.fim_vigencia" class="text-red-600 text-sm mt-1">{{ props.form.errors.fim_vigencia }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Tipo da Tabela</label>
        <select v-model="props.form.tipo_alvo" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="item">Itens</option>
          <option value="produto">Produtos</option>
        </select>
        <div v-if="props.form.errors.tipo_alvo" class="text-red-600 text-sm mt-1">{{ props.form.errors.tipo_alvo }}</div>
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

    <div>
      <button type="button" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200" @click="addItem">
        Adicionar {{ props.form.tipo_alvo === 'produto' ? 'Produto' : 'Item' }}
      </button>
    </div>

    <div class="border-t pt-4">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">
          {{ props.form.tipo_alvo === 'produto' ? 'Produtos e Preços' : 'Itens e Preços' }}
        </h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm border">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 text-left">{{ props.form.tipo_alvo === 'produto' ? 'Produto' : 'Item' }}</th>
              <th v-if="props.form.tipo_alvo === 'produto'" class="px-3 py-2 text-left">Marca</th>
              <th v-if="props.form.tipo_alvo === 'produto'" class="px-3 py-2 text-left">Fornecedor</th>
              <th class="px-3 py-2 text-left">Preço</th>
              <th class="px-3 py-2 text-left">Desconto %</th>
              <th class="px-3 py-2 text-left">Qtd. Mínima</th>
              <th class="px-3 py-2 text-left">Ação</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in props.form.itens" :key="index" class="border-t">
              <td class="px-3 py-2">
                <select v-model="row.item_id" class="border rounded px-2 py-1 w-full" @change="onProdutoChange(row)">
                  <option value="">Selecione</option>
                  <option v-for="opt in filteredOptions()" :key="opt.id" :value="opt.id">{{ opt.label }}</option>
                </select>
                <div v-if="props.form.errors[`itens.${index}.${props.form.tipo_alvo === 'produto' ? 'produto_id' : 'item_id'}`]" class="text-red-600 text-xs mt-1">
                  {{ props.form.errors[`itens.${index}.${props.form.tipo_alvo === 'produto' ? 'produto_id' : 'item_id'}`] }}
                </div>
              </td>
              <td v-if="props.form.tipo_alvo === 'produto'" class="px-3 py-2">
                <select v-model="row.marca_id" class="border rounded px-2 py-1 w-full">
                  <option value="">—</option>
                  <option v-for="m in marcasForRow(row)" :key="m.id_marca" :value="m.id_marca">
                    {{ m.nome_marca }}
                  </option>
                </select>
                <div v-if="props.form.errors[`itens.${index}.marca_id`]" class="text-red-600 text-xs mt-1">
                  {{ props.form.errors[`itens.${index}.marca_id`] }}
                </div>
              </td>
              <td v-if="props.form.tipo_alvo === 'produto'" class="px-3 py-2">
                <select v-model="row.fornecedor_id" class="border rounded px-2 py-1 w-full">
                  <option value="">—</option>
                  <option v-for="f in fornecedoresForRow(row)" :key="f.id_fornecedor" :value="f.id_fornecedor">
                    {{ f.nome_fornecedor }}
                  </option>
                </select>
                <div v-if="props.form.errors[`itens.${index}.fornecedor_id`]" class="text-red-600 text-xs mt-1">
                  {{ props.form.errors[`itens.${index}.fornecedor_id`] }}
                </div>
              </td>
              <td class="px-3 py-2">
                <input v-model="row.preco" type="number" step="0.01" min="0" class="border rounded px-2 py-1 w-full">
              </td>
              <td class="px-3 py-2">
                <input v-model="row.desconto_percent" type="number" step="0.01" min="0" max="100" class="border rounded px-2 py-1 w-full">
              </td>
              <td class="px-3 py-2">
                <input v-model="row.quantidade_minima" type="number" step="1" min="1" class="border rounded px-2 py-1 w-full">
              </td>
              <td class="px-3 py-2">
                <button type="button" class="px-2 py-1 rounded bg-red-50 text-red-600" @click="removeItem(index)">Remover</button>
              </td>
            </tr>
            <tr v-if="!props.form.itens.length">
              <td colspan="5" class="px-3 py-3 text-center text-slate-500">Nenhum item adicionado.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">{{ submitLabel }}</button>
    </div>
  </form>
</template>

