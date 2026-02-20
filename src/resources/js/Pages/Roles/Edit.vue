<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  role: { type: Object, required: true },
  permissionMenus: { type: Array, default: () => [] },
  nonCrudMenus: { type: Array, default: () => [] },
  nonCrudAccess: { type: Object, default: () => ({}) },
  permissions: { type: Array, default: () => [] },
  selectedPermissions: { type: Object, default: () => ({}) },
  statusEnabled: { type: Boolean, default: false },
})

const initialPermissions = {}
for (const menu of props.permissionMenus) {
  const raw = props.selectedPermissions?.[menu.id] ?? props.selectedPermissions?.[String(menu.id)] ?? []
  initialPermissions[menu.id] = Array.isArray(raw) ? raw.map((id) => Number(id)) : []
}

const initialNonCrud = {}
for (const menu of props.nonCrudMenus) {
  const raw = props.nonCrudAccess?.[menu.id] ?? props.nonCrudAccess?.[String(menu.id)] ?? 0
  initialNonCrud[menu.id] = Number(raw) === 1 || raw === true ? 1 : 0
}

const form = useForm({
  global_permissions: {
    status: props.statusEnabled ? 1 : 0,
    non_crud: initialNonCrud,
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
      <div class="border rounded p-3 space-y-3">
        <p class="text-sm font-semibold text-slate-700">Acesso para modulos sem CRUD</p>
        <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-3">
          <label class="inline-flex items-center gap-2 border rounded px-3 py-2">
            <input
              v-model="form.global_permissions.status"
              type="checkbox"
              :true-value="1"
              :false-value="0"
            >
            <span>Permissao global de status</span>
          </label>
          <label
            v-for="menu in nonCrudMenus"
            :key="`noncrud-${menu.id}`"
            class="inline-flex items-center gap-2 border rounded px-3 py-2"
          >
            <input
              v-model="form.global_permissions.non_crud[menu.id]"
              type="checkbox"
              :true-value="1"
              :false-value="0"
            >
            <span>{{ menu.name }}</span>
          </label>
        </div>
      </div>

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
            <tr v-for="menu in permissionMenus" :key="menu.id" class="border-t">
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
