<script setup>
import { ref } from 'vue'

const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },
  options: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['submit', 'select', 'clear-options'])

const open = ref(false)
const code = ref('')

function toggle() {
  open.value = !open.value
  if (!open.value) {
    emit('clear-options')
  }
}

function close() {
  open.value = false
  emit('clear-options')
}

async function submit() {
  const normalizedCode = String(code.value ?? '').trim()
  if (!normalizedCode) {
    return
  }

  emit('clear-options')
  emit('submit', normalizedCode)
  code.value = ''
}

function selectOption(option) {
  emit('select', option)
}

function money(value) {
  return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
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

      <div v-if="options.length" class="mt-4">
        <div class="text-sm text-gray-600 mb-2">
          Encontramos mais de um cadastro para este codigo. Selecione o estoque desejado.
        </div>
        <div class="overflow-auto border rounded-lg bg-white">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">ID Estoque</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Fornecedor</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Preco</th>
                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Acoes</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="option in options" :key="option.id_estoque">
                <td class="px-3 py-2 text-gray-700">{{ option.id_estoque }}</td>
                <td class="px-3 py-2 text-gray-700">{{ option.nome_produto }}</td>
                <td class="px-3 py-2 text-gray-700">{{ option.fornecedor || '-' }}</td>
                <td class="px-3 py-2 text-gray-700">{{ option.marca || '-' }}</td>
                <td class="px-3 py-2 text-gray-700">{{ money(option.preco_venda) }}</td>
                <td class="px-3 py-2 text-gray-700">
                  <button
                    type="button"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-md disabled:opacity-60"
                    :disabled="disabled"
                    @click="selectOption(option)"
                  >
                    Selecionar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</template>
