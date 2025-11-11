<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import TaxRuleForm from './TaxRuleForm.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

// normalmentes estas listas vêm do controller
const props = defineProps({
  ufs: Array,                 // ['SP','RJ',...]
  customerSegments: Array,    // [{id,nome}]
  productSegments: Array,      // [{id_categoria, nome_categoria}] se você usar
  channels: { type: Array, default: () => [] },
  operationTypes: { type: Array, default: () => [] },
})

// helpers
function normalizeDecimal(v) {
  if (v === '' || v === null || v === undefined) return null
  const s = String(v).trim()
  return s.includes(',') ? s.replace(/\./g, '').replace(',', '.') : s
}

const form = useForm({
  name: '',
  tax_code: '',
  scope: 1,                // 1=item | 2=frete | 3=pedido (mantendo compat com o form filho)
  priority: 10,
  starts_at: '',
  ends_at: '',
  origin_uf: '',
  dest_uf: '',
  customer_segment_id: '',
  product_segment_ids: [], // <- sem props.rule aqui!
  base: 'price',           // price | price+freight | subtotal
  method: 'percent',       // percent | fixed | formula
  rate: null,              // %
  amount: null,            // R$
  formula: '',             // quando method='formula'
  apply_mode: 'stack',     // stack | exclusive
  canal: '',
  tipo_operacao: '',
})

function submit() {
  form
    .transform(data => ({
      ...data,
      rate: normalizeDecimal(data.rate),
      amount: normalizeDecimal(data.amount),
      product_segment_ids: (data.product_segment_ids || []).map(o => o?.value ?? o),
    }))
    .post(route('taxes.store'), {
      onSuccess: () => toast.success('Regra criada com sucesso!'),
      onError: () => toast.error('Não foi possível criar. Verifique os campos.'),
    })
}
</script>

<template>
  <Head title="Nova Regra de Taxa" />

  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Nova Regra</h2>
    <Link :href="route('taxes.index')" class="px-3 py-2 rounded bg-gray-100">Voltar</Link>
  </div>

  <div class="bg-white rounded shadow p-4">
    <TaxRuleForm
      :form="form"
      :ufs="ufs"
      :customer-segments="customerSegments"
      :product-segments="productSegments"
      :channels="channels"
      :operation-types="operationTypes"
      @submit="submit"
    />
  </div>
</template>
