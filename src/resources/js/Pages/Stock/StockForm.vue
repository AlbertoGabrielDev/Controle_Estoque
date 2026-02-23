<script setup>
import axios from 'axios'
import { onBeforeUnmount, ref, watch } from 'vue'
import StockTaxPreview from './StockTaxPreview.vue'

const props = defineProps({
  form: { type: Object, required: true },
  fornecedores: { type: Array, default: () => [] },
  marcas: { type: Array, default: () => [] },
  produtos: { type: Array, default: () => [] },
  lockProduct: { type: Boolean, default: false },
  productLabel: { type: String, default: '' },
  initialVm: { type: Object, default: null },
  submitLabel: { type: String, default: 'Salvar' },
})

defineEmits(['submit'])

const previewVm = ref(props.initialVm ?? null)
let timer = null

function parseMoney(value) {
  if (value == null || value === '') return 0
  const normalized = String(value).replace(',', '.')
  const n = Number(normalized)
  return Number.isFinite(n) ? n : 0
}

async function refreshTaxes() {
  if (!props.form.id_produto_fk) {
    previewVm.value = null
    props.form.imposto_total = null
    props.form.impostos_json = ''
    props.form.id_tax_fk = null
    return
  }

  try {
    const { data } = await axios.post(route('estoque.calcImpostos'), {
      id_produto_fk: props.form.id_produto_fk,
      preco_venda: parseMoney(props.form.preco_venda),
    })

    previewVm.value = data?.vm ?? null
    props.form.imposto_total = data?.vm?.__totais?.total_impostos ?? 0
    props.form.impostos_json = JSON.stringify(data?.raw?._compact ?? data?.raw ?? {})
    props.form.id_tax_fk = data?.meta?.id_tax_fk ?? null
  } catch (error) {
    console.error('calc-impostos failed', error)
  }
}

watch(
  () => [props.form.id_produto_fk, props.form.preco_venda],
  () => {
    clearTimeout(timer)
    timer = setTimeout(() => {
      refreshTaxes()
    }, 250)
  },
  { immediate: true }
)

onBeforeUnmount(() => clearTimeout(timer))
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow space-y-4">
    <div v-if="props.lockProduct">
      <label class="block text-sm font-medium">Produto</label>
      <input :value="props.productLabel" class="mt-1 border rounded px-3 py-2 w-full bg-gray-100" disabled>
    </div>

    <div v-else>
      <label class="block text-sm font-medium">Produto</label>
      <select v-model="props.form.id_produto_fk" class="mt-1 border rounded px-3 py-2 w-full">
        <option value="">Selecione um Produto</option>
        <option v-for="p in props.produtos" :key="p.id_produto" :value="p.id_produto">
          {{ p.nome_produto }}
        </option>
      </select>
      <div v-if="props.form.errors.id_produto_fk" class="text-red-600 text-sm mt-1">{{ props.form.errors.id_produto_fk }}</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Quantidade</label>
        <input v-model="props.form.quantidade" type="number" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.quantidade" class="text-red-600 text-sm mt-1">{{ props.form.errors.quantidade }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Preço Custo</label>
        <input v-model="props.form.preco_custo" type="number" step="0.01" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.preco_custo" class="text-red-600 text-sm mt-1">{{ props.form.errors.preco_custo }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Preço Venda</label>
        <input v-model="props.form.preco_venda" type="number" step="0.01" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.preco_venda" class="text-red-600 text-sm mt-1">{{ props.form.errors.preco_venda }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">QRCode do Lote</label>
        <input
          v-model="props.form.qrcode"
          class="mt-1 border rounded px-3 py-2 w-full bg-gray-100"
          placeholder="Será gerado ao salvar"
          readonly
        >
        <div v-if="props.form.errors.qrcode" class="text-red-600 text-sm mt-1">{{ props.form.errors.qrcode }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Quantidade de Aviso</label>
        <input v-model="props.form.quantidade_aviso" type="number" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.quantidade_aviso" class="text-red-600 text-sm mt-1">{{ props.form.errors.quantidade_aviso }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Lote</label>
        <input v-model="props.form.lote" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.lote" class="text-red-600 text-sm mt-1">{{ props.form.errors.lote }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Localização</label>
        <input v-model="props.form.localizacao" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.localizacao" class="text-red-600 text-sm mt-1">{{ props.form.errors.localizacao }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Data Validade</label>
        <input v-model="props.form.validade" type="date" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.validade" class="text-red-600 text-sm mt-1">{{ props.form.errors.validade }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Data Chegada</label>
        <input v-model="props.form.data_chegada" type="date" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.data_chegada" class="text-red-600 text-sm mt-1">{{ props.form.errors.data_chegada }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Fornecedor</label>
        <select v-model="props.form.id_fornecedor_fk" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">Selecione um Fornecedor</option>
          <option v-for="f in props.fornecedores" :key="f.id_fornecedor" :value="f.id_fornecedor">
            {{ f.nome_fornecedor }}
          </option>
        </select>
        <div v-if="props.form.errors.id_fornecedor_fk" class="text-red-600 text-sm mt-1">{{ props.form.errors.id_fornecedor_fk }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Marca</label>
        <select v-model="props.form.id_marca_fk" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">Selecione uma Marca</option>
          <option v-for="m in props.marcas" :key="m.id_marca" :value="m.id_marca">
            {{ m.nome_marca }}
          </option>
        </select>
        <div v-if="props.form.errors.id_marca_fk" class="text-red-600 text-sm mt-1">{{ props.form.errors.id_marca_fk }}</div>
      </div>
    </div>

    <div class="mt-6 border rounded p-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-700">Impostos estimados</h3>
      </div>
      <div class="mt-3 text-sm text-slate-700">
        <StockTaxPreview :vm="previewVm" />
      </div>
    </div>

    <div class="flex justify-end gap-2">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">{{ submitLabel }}</button>
    </div>
  </form>
</template>
