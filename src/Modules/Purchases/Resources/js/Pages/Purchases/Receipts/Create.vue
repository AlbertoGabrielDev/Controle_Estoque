<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ReceiptForm from './ReceiptForm.vue'

const form = useForm({
  order_id: '',
  data_recebimento: '',
  observacoes: '',
  items: [
    {
      order_item_id: '',
      quantidade_recebida: 1,
      preco_unit_recebido: 0,
      imposto_id: '',
      aliquota_snapshot: '',
    },
  ],
})

/**
 * Submit the receipt creation request.
 *
 * @returns {void}
 */
function submit() {
  form.post(route('purchases.receipts.store'))
}
</script>

<template>
  <Head title="Novo Recebimento" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Novo Recebimento</h1>
    <Link :href="route('purchases.receipts.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ReceiptForm :form="form" submit-label="Registrar Recebimento" @submit="submit" />
</template>
