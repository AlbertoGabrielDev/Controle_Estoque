<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import CentroForm from './Form.vue'

const props = defineProps({
  centrosPai: { type: Array, default: () => [] },
})

const form = useForm({
  codigo: '',
  nome: '',
  centro_pai_id: '',
  ativo: true,
})

function submit() {
  form.post(route('centros_custo.store'))
}
</script>

<template>
  <Head :title="$t('New Cost Center')" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">{{ $t('New Cost Center') }}</h1>
    <Link :href="route('centros_custo.index')" class="text-blue-600">{{ $t('Back') }}</Link>
  </div>

  <CentroForm
    :form="form"
    :centros-pai="props.centrosPai"
    :submit-label="$t('Create')"
    @submit="submit"
  />
</template>
