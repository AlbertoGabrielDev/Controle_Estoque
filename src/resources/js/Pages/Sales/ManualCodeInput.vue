<script setup>
import { ref } from 'vue'

const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['submit'])

const open = ref(false)
const code = ref('')

function toggle() {
  open.value = !open.value
}

function close() {
  open.value = false
}

async function submit() {
  const normalizedCode = String(code.value ?? '').trim()
  if (!normalizedCode) {
    return
  }

  emit('submit', normalizedCode)
  code.value = ''
}

function onKeydown(event) {
  if (event.key === 'Enter') {
    submit()
  }

  if (event.key === 'Escape') {
    close()
  }
}
</script>

<template>
  <section class="mt-4">
    <button
      type="button"
      class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-base font-medium disabled:opacity-60"
      :disabled="disabled"
      @click="toggle"
    >
      {{ open ? 'Ocultar codigo manual' : 'Inserir codigo manual' }}
    </button>

    <div v-if="open" class="bg-gray-50 border rounded-lg p-3 md:p-4 mt-3">
      <div class="flex flex-col md:flex-row items-end gap-3">
        <div class="w-full md:w-80">
          <label for="manual-code" class="block text-sm font-medium text-gray-700">
            Codigo do produto
          </label>
          <input
            id="manual-code"
            v-model="code"
            type="text"
            placeholder="Ex.: ABC123"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2"
            @keydown="onKeydown"
          >
        </div>

        <div class="flex gap-2 w-full md:w-auto">
          <button
            type="button"
            class="flex-1 md:flex-none bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg disabled:opacity-60"
            :disabled="disabled"
            @click="submit"
          >
            Adicionar
          </button>
          <button
            type="button"
            class="flex-1 md:flex-none bg-white border hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg"
            @click="close"
          >
            Fechar
          </button>
        </div>
      </div>
    </div>
  </section>
</template>
