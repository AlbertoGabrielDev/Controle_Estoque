<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ReturnForm from './ReturnForm.vue'

const form = useForm({
  receipt_id: '',
  order_id: '',
  motivo: '',
  data_devolucao: '',
  items: [
    {
      receipt_item_id: '',
      order_item_id: '',
      item_id: '',
      quantidade_devolvida: 1,
      observacoes: '',
    },
  ],
})

/**
 * Submit the return creation request.
 *
 * @returns {void}
 */
function submit() {
  form.post(route('purchases.returns.store'))
}
</script>

<template>
  <Head title="Nova Devolucao" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Nova Devolucao</h1>
    <Link :href="route('purchases.returns.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ReturnForm :form="form" submit-label="Criar Devolucao" @submit="submit" />
</template>
