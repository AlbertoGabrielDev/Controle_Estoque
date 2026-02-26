<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import RequisitionForm from './RequisitionForm.vue'

const props = defineProps({
  requisition: { type: Object, required: true },
})

const form = useForm({
  observacoes: props.requisition.observacoes ?? '',
  data_requisicao: props.requisition.data_requisicao ?? '',
  items: (props.requisition.items ?? []).map((item) => ({
    item_id: item.item_id ?? '',
    descricao_snapshot: item.descricao_snapshot ?? '',
    unidade_medida_id: item.unidade_medida_id ?? '',
    quantidade: item.quantidade ?? 0,
    preco_estimado: item.preco_estimado ?? 0,
    imposto_id: item.imposto_id ?? '',
    observacoes: item.observacoes ?? '',
  })),
})

/**
 * Submit the requisition update request.
 *
 * @returns {void}
 */
function submit() {
  form.patch(route('purchases.requisitions.update', props.requisition.id))
}
</script>

<template>
  <Head title="Editar Requisicao" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Requisicao</h1>
    <Link :href="route('purchases.requisitions.show', props.requisition.id)" class="text-blue-600">Voltar</Link>
  </div>

  <RequisitionForm :form="form" submit-label="Salvar Alteracoes" @submit="submit" />
</template>
