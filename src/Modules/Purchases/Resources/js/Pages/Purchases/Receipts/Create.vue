<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ReceiptForm from './ReceiptForm.vue'

const props = defineProps({
  orders_options: { type: Array, default: () => [] },
})

const form = useForm({
  order_id: '',
  data_recebimento: new Date().toISOString().split('T')[0],
  observacoes: '',
  items: [],
})

/**
 * Submit the receipt creation request.
 *
 * @returns {void}
 */
function submit() {
  form.transform((data) => ({
    ...data,
    items: data.items.filter(item => Number(item.quantidade_recebida) > 0)
  })).post(route('purchases.receipts.store'))
}
</script>

<template>
  <Head title="Novo Recebimento" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Novo Recebimento</h1>
    <Link :href="route('purchases.receipts.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ReceiptForm 
    :form="form" 
    :orders-options="props.orders_options"
    submit-label="Registrar Recebimento" 
    @submit="submit" 
  />
</template>

