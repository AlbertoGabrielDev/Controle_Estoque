<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
const form = useForm({ nome: '' })
const submit = () => form.post(route('segmentos.store'))
</script>

<template>
  <Head title="Novo Segmento" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Novo Segmento</h1>
    <Link :href="route('segmentos.index')" class="text-blue-600">Voltar</Link>
  </div>

  <form @submit.prevent="submit" class="bg-white p-4 rounded shadow max-w-xl">
    <label class="block text-sm font-medium">Nome</label>
    <input v-model="form.nome" class="mt-1 border rounded px-3 py-2 w-full" />
    <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">{{ form.errors.nome }}</div>

    <div class="mt-4 flex justify-end gap-2">
      <Link :href="route('segmentos.index')" class="px-3 py-2 rounded border">Cancelar</Link>
      <button :disabled="form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">Salvar</button>
    </div>
  </form>
</template>
