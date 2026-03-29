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
  <Head :title="$t('New Measurement Unit')" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">{{ $t('New Measurement Unit') }}</h1>
    <Link :href="route('unidades_medida.index')" class="text-blue-600">{{ $t('Back') }}</Link>
  </div>

  <UnitForm :form="form" :unidades-base="props.unidadesBase" :submit-label="$t('Create Unit')" @submit="submit" />
</template>
