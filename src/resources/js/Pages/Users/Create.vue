<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import UserForm from './UserForm.vue'

defineProps({
  roles: { type: Array, default: () => [] },
  units: { type: Array, default: () => [] },
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  id_unidade: '',
  roles: [],
  photo: null,
  current_photo: '',
})

function submit() {
  form.post(route('usuario.inserirUsuario'), {
    forceFormData: true,
  })
}
</script>

<template>
  <Head title="Cadastro de Usuario" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Cadastro de Usuario</h1>
    <Link :href="route('usuario.index')" class="text-blue-600">Voltar</Link>
  </div>

  <UserForm
    :form="form"
    :roles="roles"
    :units="units"
    submit-label="Cadastrar Usuario"
    @submit="submit"
  />
</template>

