<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import TaxRuleForm from './TaxRuleForm.vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
  rule: Object,
  taxes: Object,
  ufs: Array,
  customerSegments: Array,
  productSegments: Array,
  channels: { type: Array, default: () => [] },
  operationTypes: { type: Array, default: () => [] },
})
const toast = useToast()
const toOptions = (segments) =>
  (segments || []).map(s => ({ value: Number(s.id_categoria), label: s.nome_categoria }))

const productOptions = toOptions(props.productSegments)

// ids vindos do backend (ex.: [1, 3, 5])
const selectedIds = Array.isArray(props.rule?.product_segment_ids)
  ? props.rule.product_segment_ids.map(Number)
  : []

// objetos selecionados para o multiselect
const selectedObjects = productOptions.filter(o => selectedIds.includes(o.value))
function dbBaseToUi(baseFormula) {
  switch (baseFormula) {
    case 'valor_menos_desc': return 'price'
    case 'valor_mais_frete': return 'price+freight'
    case 'valor': return 'subtotal'
    default: return 'price'
  }
}
function normalizeDecimal(v) {
  if (v === '' || v === null || v === undefined) return null
  const s = String(v).trim()
  if (s.includes(',')) {
    // "1.234,56" -> "1234.56" ; "2,23" -> "2.23"
    return s.replace(/\./g, '').replace(',', '.')
  }
  // Já está com ponto decimal ("2.23") -> mantém
  return s
}
function dbMethodToUi(metodo) {
  switch (Number(metodo)) {
    case 1: return 'percent'
    case 2: return 'fixed'
    case 3: return 'formula'
    default: return 'percent'
  }
}
const form = useForm({
  name: props.taxes?.nome ?? '',
  tax_code: props.taxes?.codigo ?? '',
  scope: Number(props.rule?.escopo ?? 1),
  priority: props.rule?.prioridade ?? 0,
  starts_at: props.rule?.vigencia_inicio ?? '',
  ends_at: props.rule?.vigencia_fim ?? '',
  origin_uf: props.rule?.uf_origem ?? '',
  dest_uf: props.rule?.uf_destino ?? '',
  customer_segment_id: props.rule?.segment_id ?? '',
  product_segment_ids: selectedObjects,
  base: dbBaseToUi(props.rule?.base_formula ?? 'valor_menos_desc'),
  method: dbMethodToUi(props.rule?.metodo ?? 1),
  rate: props.rule?.aliquota_percent ?? null,
  amount: props.rule?.valor_fixo ?? null,
  formula: props.rule?.expression ?? '',
  apply_mode: (props.rule?.cumulativo ? 'stack' : 'exclusive'),
  canal: props.rule.canal ?? '',
  tipo_operacao: props.rule.tipo_operacao ?? '',
})

function submit() {
  form
    .transform(data => ({
      ...data,
      rate: normalizeDecimal(data.rate),
      amount: normalizeDecimal(data.amount),
      // Se o multiselect está com objetos {value,label}, converta para IDs:
      product_segment_ids: (data.product_segment_ids || []).map(o => o?.value ?? o),
    }))
    .put(route('taxes.update', props.rule.id), {
      onSuccess: () => toast.success('Regra salva com sucesso!'),
      onError: () => toast.error('Não foi possível salvar. Verifique os campos.'),
    })
}
</script>

<template>

  <Head title="Editar Regra de Taxa" />

  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Editar Regra</h2>
    <Link :href="route('taxes.index')" class="px-3 py-2 rounded bg-gray-100">Voltar</Link>
  </div>

  <div class="bg-white rounded shadow p-4">
    <TaxRuleForm :form="form" :ufs="ufs" :customer-segments="customerSegments" :product-segments="productSegments"
      :channels="channels" :operation-types="operationTypes" @submit="submit" />
  </div>
</template>
