<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({ nome: '', tipo: 'item', percentual_maximo: 0, ativo: true })
function submit() { form.post(route('commercial.discount-policies.store')) }
</script>

<template>
  <Head :title="$t('New Discount Policy')" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('New Discount Policy') }}</h1>
    <Link :href="route('commercial.discount-policies.index')" class="text-blue-600 dark:text-blue-400">{{ $t('Back') }}</Link>
  </div>
  <form @submit.prevent="submit" class="bg-white p-4 rounded shadow dark:bg-slate-900 dark:border dark:border-slate-700 space-y-4 max-w-lg text-slate-700 dark:text-slate-100">
    <div>
      <label class="block text-sm font-medium">{{ $t('Name') }} *</label>
      <input v-model="form.nome" type="text" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" required>
      <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">{{ form.errors.nome }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium">{{ $t('Type') }} *</label>
      <select v-model="form.tipo" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
        <option value="item">{{ $t('Per Item') }}</option>
        <option value="pedido">{{ $t('Per Order') }}</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">{{ $t('Maximum Discount (%)') }} *</label>
      <input v-model="form.percentual_maximo" type="number" step="0.01" min="0" max="100" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" required>
    </div>
    <div class="flex items-center gap-2">
      <input v-model="form.ativo" type="checkbox" id="ativo" class="rounded">
      <label for="ativo" class="text-sm font-medium">{{ $t('Active') }}</label>
    </div>
    <div class="flex justify-end">
      <button :disabled="form.processing" type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">{{ $t('Create') }}</button>
    </div>
  </form>
</template>
