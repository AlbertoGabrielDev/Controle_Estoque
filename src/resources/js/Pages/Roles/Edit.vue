<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  role: { type: Object, required: true },
  menus: { type: Array, default: () => [] },
  permissions: { type: Array, default: () => [] },
  selectedPermissions: { type: Object, default: () => ({}) },
  statusEnabled: { type: Boolean, default: false },
})

const initialPermissions = {}
for (const menu of props.menus) {
  const raw = props.selectedPermissions?.[menu.id] ?? props.selectedPermissions?.[String(menu.id)] ?? []
  initialPermissions[menu.id] = Array.isArray(raw) ? raw.map((id) => Number(id)) : []
}

const form = useForm({
  global_permissions: {
    status: props.statusEnabled ? 1 : 0,
  },
  permissions: initialPermissions,
})

function submit() {
  form.put(route('roles.salvarEditar', props.role.id))
}
</script>

<template>
  <Head :title="`Permissoes da role ${role.name}`" />

  <div class="max-w-6xl bg-white p-4 rounded shadow">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Editar Permissoes: {{ role.name }}</h1>
      <Link :href="route('roles.index')" class="text-blue-600">Voltar</Link>
    </div>

    <form @submit.prevent="submit" class="space-y-4">
      <label class="inline-flex items-center gap-2 border rounded px-3 py-2">
        <input
          v-model="form.global_permissions.status"
          type="checkbox"
          :true-value="1"
          :false-value="0"
        >
        <span>Permissao global de status</span>
      </label>

      <div class="overflow-x-auto border rounded">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="p-2 text-left">Menu</th>
              <th
                v-for="permission in permissions"
                :key="permission.id"
                class="p-2 text-center"
              >
                {{ permission.name }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="menu in menus" :key="menu.id" class="border-t">
              <td class="p-2 font-medium">{{ menu.name }}</td>
              <td
                v-for="permission in permissions"
                :key="`${menu.id}-${permission.id}`"
                class="p-2 text-center"
              >
                <input
                  v-model="form.permissions[menu.id]"
                  type="checkbox"
                  :value="permission.id"
                >
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-end">
        <button :disabled="form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">Salvar Permissoes</button>
      </div>
    </form>
  </div>
</template>

