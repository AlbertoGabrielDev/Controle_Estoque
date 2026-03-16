<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import BrandForm from './BrandForm.vue'

const form = useForm({
  nome_marca: '',
})

function submit() {
  form.post(route('marca.inserirMarca'))
}
</script>

<template>
  <Head :title="$t('Brand Registration')" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">{{ $t('Brand Registration') }}</h1>
    <Link :href="route('marca.index')" class="text-blue-600">{{ $t('Back') }}</Link>
  </div>

  <BrandForm :form="form" :submit-label="$t('Create Brand')" @submit="submit" />
</template>
