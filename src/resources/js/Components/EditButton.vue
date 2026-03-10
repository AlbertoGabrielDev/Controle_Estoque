<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  routeName: { type: String, required: true },   // ex: 'clientes.edit'
  modelId:   { type: [String, Number], required: true },
  canEdit:   { type: Boolean, default: true }     // controle de permissão vindo do servidor
})
</script>

<template>
  <template v-if="canEdit">
    <Link
      :href="route(routeName, modelId)"
      class="p-2 text-cyan-600 hover:bg-cyan-50 rounded-md inline-flex items-center"
      title="Editar"
    >
      <i class="fas fa-edit"></i>
    </Link>
  </template>
</template>
