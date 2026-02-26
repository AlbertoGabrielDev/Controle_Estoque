<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import QuotationForm from './QuotationForm.vue'

const form = useForm({
  requisition_id: '',
  data_limite: '',
  observacoes: '',
  supplier_ids: [],
})

/**
 * Submit the quotation creation request.
 *
 * @returns {void}
 */
function submit() {
  form.post(route('purchases.quotations.store'))
}
</script>

<template>
  <Head title="Nova Cotacao" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Nova Cotacao</h1>
    <Link :href="route('purchases.quotations.index')" class="text-blue-600">Voltar</Link>
  </div>

  <QuotationForm :form="form" submit-label="Criar Cotacao" :show-suppliers="true" @submit="submit" />
</template>
