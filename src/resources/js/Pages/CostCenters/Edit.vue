<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import CentroForm from './Form.vue'

const props = defineProps({
  centro: { type: Object, required: true },
  centrosPai: { type: Array, default: () => [] },
})

const form = useForm({
  codigo: props.centro?.codigo ?? '',
  nome: props.centro?.nome ?? '',
  centro_pai_id: props.centro?.centro_pai_id ?? '',
  ativo: props.centro?.ativo ?? true,
})

function submit() {
  form.put(route('centros_custo.update', props.centro.id))
}
</script>

<template>
  <Head title="Editar Centro de Custo" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Centro de Custo</h1>
    <Link :href="route('centros_custo.index')" class="text-blue-600">Voltar</Link>
  </div>

  <CentroForm :form="form" :centros-pai="props.centrosPai" submit-label="Salvar Alterações" @submit="submit" />
</template>
