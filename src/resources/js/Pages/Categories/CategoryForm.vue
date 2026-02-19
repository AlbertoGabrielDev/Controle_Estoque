<script setup>
const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Salvar' },
  showImage: { type: Boolean, default: false },
})

defineEmits(['submit'])

function onImageChange(event) {
  const file = event.target.files?.[0] ?? null
  props.form.imagem = file
}
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow max-w-xl">
    <label class="block text-sm font-medium">Nome da Categoria</label>
    <input v-model="props.form.nome_categoria" class="mt-1 border rounded px-3 py-2 w-full">
    <div v-if="props.form.errors.nome_categoria" class="text-red-600 text-sm mt-1">{{ props.form.errors.nome_categoria }}</div>

    <div v-if="showImage" class="mt-4">
      <label class="block text-sm font-medium">Imagem</label>
      <input type="file" class="mt-1 border rounded px-3 py-2 w-full" @change="onImageChange">
      <div v-if="props.form.errors.imagem" class="text-red-600 text-sm mt-1">{{ props.form.errors.imagem }}</div>
    </div>

    <div class="mt-4 flex justify-end gap-2">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">{{ submitLabel }}</button>
    </div>
  </form>
</template>
