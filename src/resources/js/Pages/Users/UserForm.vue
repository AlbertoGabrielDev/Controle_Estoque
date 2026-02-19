<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  roles: { type: Array, default: () => [] },
  units: { type: Array, default: () => [] },
  submitLabel: { type: String, default: 'Salvar' },
  editing: { type: Boolean, default: false },
})

defineEmits(['submit'])

const fileInput = ref(null)
const localPreview = ref('')

const imagePreview = computed(() => localPreview.value || props.form.current_photo || '')

function pickFile() {
  fileInput.value?.click()
}

function onPhotoChange(event) {
  const file = event.target.files?.[0]
  if (!file) {
    return
  }

  props.form.photo = file
  localPreview.value = URL.createObjectURL(file)
}
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow max-w-4xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium">Nome</label>
        <input v-model="props.form.name" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.name" class="text-red-600 text-sm mt-1">{{ props.form.errors.name }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Email</label>
        <input v-model="props.form.email" type="email" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.email" class="text-red-600 text-sm mt-1">{{ props.form.errors.email }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Senha</label>
        <input
          v-model="props.form.password"
          type="password"
          class="mt-1 border rounded px-3 py-2 w-full"
          :placeholder="editing ? 'Deixe em branco para manter a senha atual' : ''"
        >
        <div v-if="props.form.errors.password" class="text-red-600 text-sm mt-1">{{ props.form.errors.password }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Unidade</label>
        <select v-model="props.form.id_unidade" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">Selecione</option>
          <option v-for="unit in units" :key="unit.id_unidade" :value="unit.id_unidade">{{ unit.nome }}</option>
        </select>
        <div v-if="props.form.errors.id_unidade" class="text-red-600 text-sm mt-1">{{ props.form.errors.id_unidade }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Perfis</label>
        <select v-model="props.form.roles" multiple class="mt-1 border rounded px-3 py-2 w-full min-h-28">
          <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
        </select>
        <div v-if="props.form.errors.roles" class="text-red-600 text-sm mt-1">{{ props.form.errors.roles }}</div>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium">Foto</label>
        <div class="mt-2 flex items-center gap-3">
          <img v-if="imagePreview" :src="imagePreview" alt="preview" class="w-16 h-16 rounded-full object-cover">
          <div v-else class="w-16 h-16 rounded-full bg-gray-100 border"></div>

          <button type="button" class="rounded-md bg-white px-3 py-2 text-sm border hover:bg-gray-50" @click="pickFile">
            Selecionar arquivo
          </button>
          <input ref="fileInput" type="file" class="hidden" accept="image/*" @change="onPhotoChange">
        </div>
        <div v-if="props.form.errors.photo" class="text-red-600 text-sm mt-1">{{ props.form.errors.photo }}</div>
      </div>
    </div>

    <div class="mt-4 flex justify-end gap-2">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">{{ submitLabel }}</button>
    </div>
  </form>
</template>

