<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import ImpostoForm from './Form.vue'

const props = defineProps({
  imposto: { type: Object, required: true },
})

const form = useForm({
  codigo: props.imposto?.codigo ?? '',
  nome: props.imposto?.nome ?? '',
  tipo: props.imposto?.tipo ?? 'IVA',
  aliquota_percent: props.imposto?.aliquota_percent ?? 0,
  ativo: props.imposto?.ativo ?? true,
})

function submit() {
  form.put(route('impostos.update', props.imposto.id))
}
</script>

<template>
  <Head title="Editar Imposto" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Imposto</h1>
    <Link :href="route('impostos.index')" class="text-blue-600">Voltar</Link>
  </div>

  <ImpostoForm :form="form" submit-label="Salvar Alterações" @submit="submit" />
</template>
