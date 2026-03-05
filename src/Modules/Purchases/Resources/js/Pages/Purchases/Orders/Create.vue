<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import OrderForm from './OrderForm.vue'

const props = defineProps({
  requisition: { type: Object, default: null },
  requisitions_options: { type: Array, default: () => [] },
  suppliers_options: { type: Array, default: () => [] },
})

const form = useForm({
  requisition_id: props.requisition ? props.requisition.id : '',
  supplier_id: '',
  data_prevista: '',
  observacoes: '',
})

/**
 * Submit the order creation request.
 *
 * @returns {void}
 */
function submit() {
  form.post(route('purchases.orders.fromRequisition'))
}
</script>

<template>
  <Head title="Novo Pedido de Compra" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Novo Pedido de Compra</h1>
    <Link :href="route('purchases.orders.index')" class="text-blue-600">Voltar</Link>
  </div>

  <OrderForm 
    :form="form" 
    :requisitions-options="props.requisitions_options"
    :suppliers-options="props.suppliers_options"
    :requisition="props.requisition"
    submit-label="Criar Pedido" 
    @submit="submit" 
  />
</template>
