<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import UserForm from './UserForm.vue'

const props = defineProps({
  usuario: { type: Object, required: true },
  roles: { type: Array, default: () => [] },
  units: { type: Array, default: () => [] },
})

function resolveCurrentPhoto(path) {
  if (!path) {
    return ''
  }

  if (String(path).startsWith('/') || String(path).startsWith('http://') || String(path).startsWith('https://')) {
    return path
  }

  return `/img/usuario/${path}`
}

const form = useForm({
  _method: 'put',
  name: props.usuario?.name ?? '',
  email: props.usuario?.email ?? '',
  password: '',
  id_unidade: props.usuario?.id_unidade_fk ?? '',
  roles: (props.usuario?.roles ?? []).map((role) => role.id),
  photo: null,
  current_photo: resolveCurrentPhoto(props.usuario?.profile_photo_path ?? ''),
})

function submit() {
  form.post(route('usuario.salvarEditar', props.usuario.id), {
    forceFormData: true,
  })
}
</script>

<template>
  <Head title="Editar Usuario" />

  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Editar Usuario</h1>
    <Link :href="route('usuario.index')" class="text-blue-600">Voltar</Link>
  </div>

  <UserForm
    :form="form"
    :roles="roles"
    :units="units"
    :editing="true"
    submit-label="Salvar Alteracoes"
    @submit="submit"
  />
</template>

