<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import UnitForm from './UnitForm.vue'

const props = defineProps({
  unidade: { type: Object, required: true },
})

const form = useForm({
  nome: props.unidade?.nome ?? '',
})

function submit() {
  form.post(route('unidades.salvarEditar', props.unidade.id_unidade))
}
</script>

<template>
  <Head title="Editar Unidade" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Unidade</h1>
    <Link :href="route('unidade.index')" class="text-blue-600">Voltar</Link>
  </div>

  <UnitForm :form="form" submit-label="Salvar Alterações" @submit="submit" />
</template>
