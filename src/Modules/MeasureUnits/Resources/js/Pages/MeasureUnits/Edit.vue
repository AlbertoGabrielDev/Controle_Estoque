<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import UnitForm from './Form.vue'

const props = defineProps({
  unidade: { type: Object, required: true },
  unidadesBase: { type: Array, default: () => [] },
})

const form = useForm({
  codigo: props.unidade?.codigo ?? '',
  descricao: props.unidade?.descricao ?? '',
  fator_base: props.unidade?.fator_base ?? 1,
  unidade_base_id: props.unidade?.unidade_base_id ?? '',
  ativo: props.unidade?.ativo ?? true,
})

function submit() {
  form.put(route('unidades_medida.update', props.unidade.id))
}
</script>

<template>
  <Head :title="$t('Edit Measurement Unit')" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">{{ $t('Edit Measurement Unit') }}</h1>
    <Link :href="route('unidades_medida.index')" class="text-blue-600">{{ $t('Back') }}</Link>
  </div>

  <UnitForm :form="form" :unidades-base="props.unidadesBase" :submit-label="$t('Save Changes')" @submit="submit" />
</template>
