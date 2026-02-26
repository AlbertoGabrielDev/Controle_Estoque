<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import QuotationForm from './QuotationForm.vue'

const props = defineProps({
  quotation: { type: Object, required: true },
})

const form = useForm({
  requisition_id: props.quotation.requisition_id,
  data_limite: props.quotation.data_limite ?? '',
  observacoes: props.quotation.observacoes ?? '',
  supplier_ids: [],
})

/**
 * Submit the quotation update request.
 *
 * @returns {void}
 */
function submit() {
  form.patch(route('purchases.quotations.update', props.quotation.id))
}
</script>

<template>
  <Head title="Editar Cotacao" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Cotacao</h1>
    <Link :href="route('purchases.quotations.show', props.quotation.id)" class="text-blue-600">Voltar</Link>
  </div>

  <QuotationForm
    :form="form"
    submit-label="Salvar Alteracoes"
    :show-suppliers="false"
    :readonly-requisition="true"
    @submit="submit"
  />
</template>
