<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import TaxRuleForm from './TaxRuleForm.vue'

// normalmentes estas listas vêm do controller
const props = defineProps({
  ufs: Array,                 // ['SP','RJ',...]
  customerSegments: Array,    // [{id,nome}]
  productSegments: Array      // [{id,nome}] se você usar
})

const form = useForm({
  name: '',
  tax_code: '',
  scope: 'item',            // item | shipping | order
  priority: 10,
  active: true,
  starts_at: '',
  ends_at: '',
  origin_uf: '',
  dest_uf: '',
  customer_segment_id: '',
  product_segment_ids: Array.isArray(props.rule.product_segment_ids)
    ? [...props.rule.product_segment_ids]
    : [],
  base: 'price',            // price | price+freight | subtotal ...
  method: 'percent',        // percent | fixed | formula
  rate: null,               // %
  amount: null,             // valor fixo
  formula: '',              // quando method='formula'
  apply_mode: 'stack',      // stack | exclusive
})

function submit() {
  form.post(route('taxes.store'))
}
</script>

<template>

  <Head title="Nova Regra de Taxa" />

  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Nova Regra</h2>
    <Link :href="route('taxes.index')" class="px-3 py-2 rounded bg-gray-100">Voltar</Link>
  </div>

  <div class="bg-white rounded shadow p-4">
    <TaxRuleForm :form="form" :ufs="ufs" :customer-segments="customerSegments" :product-segments="productSegments"
      @submit="submit" />
  </div>
</template>
