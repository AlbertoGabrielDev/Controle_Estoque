<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'load-cart'])

const localClient = ref(props.modelValue)

watch(
  () => props.modelValue,
  (value) => {
    if (value !== localClient.value) {
      localClient.value = value ?? ''
    }
  }
)

watch(localClient, (value) => {
  emit('update:modelValue', String(value ?? ''))
})

function sanitizeClient() {
  localClient.value = String(localClient.value ?? '').replace(/[^\d]/g, '').slice(0, 20)
}
</script>

<template>
  <section class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
    <div class="md:col-span-2">
      <label for="sales-client" class="block text-sm font-medium text-gray-700">
        Cliente (WhatsApp)
        <span v-if="!required" class="ml-1 text-xs text-gray-500">(Opcional)</span>
      </label>
      <input
        id="sales-client"
        v-model="localClient"
        type="text"
        inputmode="numeric"
        maxlength="20"
        placeholder="Ex.: 5599999999999"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
        @input="sanitizeClient"
      >
    </div>

    <button
      type="button"
      class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-base font-medium disabled:opacity-60"
      :disabled="loading"
      @click="$emit('load-cart')"
    >
      {{ loading ? 'Carregando...' : 'Carregar Carrinho' }}
    </button>
  </section>
</template>
