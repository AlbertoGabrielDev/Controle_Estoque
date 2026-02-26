<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import RequisitionForm from './RequisitionForm.vue'

const form = useForm({
  observacoes: '',
  data_requisicao: '',
  items: [
    {
      item_id: '',
      descricao_snapshot: '',
      unidade_medida_id: '',
      quantidade: 1,
      preco_estimado: 0,
      imposto_id: '',
      observacoes: '',
    },
  ],
})

/**
 * Submit the requisition creation request.
 *
 * @returns {void}
 */
function submit() {
  form.post(route('purchases.requisitions.store'))
}
</script>

<template>
  <Head title="Nova Requisicao" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Nova Requisicao</h1>
    <Link :href="route('purchases.requisitions.index')" class="text-blue-600">Voltar</Link>
  </div>

  <RequisitionForm :form="form" submit-label="Criar Requisicao" @submit="submit" />
</template>
