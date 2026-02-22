<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import UnitForm from './Form.vue'

const props = defineProps({
  unidadesBase: { type: Array, default: () => [] },
})

const form = useForm({
  codigo: '',
  descricao: '',
  fator_base: 1,
  unidade_base_id: '',
  ativo: true,
})

function submit() {
  form.post(route('unidades_medida.store'))
}
</script>

<template>
  <Head title="Nova Unidade de Medida" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Nova Unidade de Medida</h1>
    <Link :href="route('unidades_medida.index')" class="text-blue-600">Voltar</Link>
  </div>

  <UnitForm :form="form" :unidades-base="props.unidadesBase" submit-label="Criar Unidade" @submit="submit" />
</template>
