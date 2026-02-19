<script setup>
const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Salvar' },
})

defineEmits(['submit'])
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow max-w-xl">
    <label class="block text-sm font-medium">Nome da Role</label>
    <input v-model="props.form.name" class="mt-1 border rounded px-3 py-2 w-full">
    <div v-if="props.form.errors.name" class="text-red-600 text-sm mt-1">{{ props.form.errors.name }}</div>

    <div class="mt-4 flex justify-end gap-2">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">{{ submitLabel }}</button>
    </div>
  </form>
</template>

