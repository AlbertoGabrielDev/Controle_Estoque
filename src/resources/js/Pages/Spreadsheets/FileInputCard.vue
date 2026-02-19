<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  fileName: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['select'])

function onChange(event) {
  const file = event.target.files?.[0] ?? null
  emit('select', file)
}
</script>

<template>
  <section class="rounded-lg border bg-white p-4">
    <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>

    <label class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded-md border border-dashed border-blue-300 bg-blue-50 p-4 text-center hover:bg-blue-100">
      <span class="text-sm font-medium text-blue-700">
        {{ fileName || 'Selecionar arquivo .xlsx/.csv' }}
      </span>
      <input
        type="file"
        accept=".xlsx,.csv"
        class="hidden"
        :disabled="disabled"
        @change="onChange"
      >
    </label>
  </section>
</template>
