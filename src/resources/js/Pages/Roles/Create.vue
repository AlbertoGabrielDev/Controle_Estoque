<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import RoleForm from './RoleForm.vue'

const form = useForm({
  name: '',
})

function submit() {
  form.post(route('roles.inserirRole'))
}
</script>

<template>
  <Head title="Cadastro de Role" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Cadastro de Role</h1>
    <Link :href="route('roles.index')" class="text-blue-600">Voltar</Link>
  </div>

  <RoleForm :form="form" submit-label="Cadastrar Role" @submit="submit" />
</template>

