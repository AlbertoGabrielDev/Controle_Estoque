<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
const props = defineProps({ segmento: Object })
const form = useForm({ nome: props.segmento.nome })
const submit = () => form.put(route('segmentos.update', props.segmento.id))
</script>

<template>
  <Head title="Editar Segmento" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Segmento</h1>
    <Link :href="route('segmentos.index')" class="text-blue-600">Voltar</Link>
  </div>

  <form @submit.prevent="submit" class="bg-white p-4 rounded shadow max-w-xl">
    <label class="block text-sm font-medium">Nome</label>
    <input v-model="form.nome" class="mt-1 border rounded px-3 py-2 w-full" />
    <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">{{ form.errors.nome }}</div>

    <div class="mt-4 flex justify-between">
      <Link
        as="button"
        method="delete"
        :href="route('segmentos.destroy', props.segmento.id)"
        class="px-3 py-2 rounded border border-red-600 text-red-600"
      >
        Excluir
      </Link>

      <div class="flex gap-2">
        <Link :href="route('segmentos.index')" class="px-3 py-2 rounded border">Cancelar</Link>
        <button :disabled="form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">Salvar</button>
      </div>
    </div>
  </form>
</template>
