<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  columnsOne: {
    type: Array,
    default: () => [],
  },
  columnsTwo: {
    type: Array,
    default: () => [],
  },
  operators: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  results: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['compare'])

const columnOne = ref('')
const columnTwo = ref('')
const operator = ref('')

watch(
  () => props.columnsOne,
  (value) => {
    if (!value.length) {
      columnOne.value = ''
      return
    }

    if (!value.includes(columnOne.value)) {
      columnOne.value = value[0]
    }
  },
  { immediate: true }
)

watch(
  () => props.columnsTwo,
  (value) => {
    if (!value.length) {
      columnTwo.value = ''
      return
    }

    if (!value.includes(columnTwo.value)) {
      columnTwo.value = value[0]
    }
  },
  { immediate: true }
)

watch(
  () => props.operators,
  (value) => {
    if (!value.length) {
      operator.value = ''
      return
    }

    const values = value.map((item) => item.value)
    if (!values.includes(operator.value)) {
      operator.value = value[0].value
    }
  },
  { immediate: true }
)

const canCompare = computed(() => Boolean(columnOne.value && columnTwo.value && operator.value))

function compare() {
  if (!canCompare.value) {
    return
  }

  emit('compare', {
    coluna1: columnOne.value,
    coluna2: columnTwo.value,
    operador: operator.value,
  })
}

function formatResult(item) {
  if (Array.isArray(item)) {
    return item.join(' - ')
  }

  if (typeof item === 'object' && item !== null) {
    return JSON.stringify(item)
  }

  return String(item ?? '')
}
</script>

<template>
  <section class="mt-6 rounded-lg border bg-white p-4">
    <h3 class="text-lg font-semibold text-gray-800">Comparacao de Dados</h3>

    <div class="mt-3 flex flex-wrap items-end gap-3">
      <div class="flex flex-col">
        <label class="text-sm text-gray-600">Coluna Planilha 1</label>
        <select v-model="columnOne" class="mt-1 rounded-md border px-3 py-2 text-sm">
          <option v-for="column in columnsOne" :key="`c1-${column}`" :value="column">{{ column }}</option>
        </select>
      </div>

      <div class="flex flex-col">
        <label class="text-sm text-gray-600">Operador</label>
        <select v-model="operator" class="mt-1 rounded-md border px-3 py-2 text-sm">
          <option
            v-for="item in operators"
            :key="`op-${item.value}`"
            :value="item.value"
          >
            {{ item.label }}
          </option>
        </select>
      </div>

      <div class="flex flex-col">
        <label class="text-sm text-gray-600">Coluna Planilha 2</label>
        <select v-model="columnTwo" class="mt-1 rounded-md border px-3 py-2 text-sm">
          <option v-for="column in columnsTwo" :key="`c2-${column}`" :value="column">{{ column }}</option>
        </select>
      </div>

      <button
        type="button"
        class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-60"
        :disabled="loading || !canCompare"
        @click="compare"
      >
        {{ loading ? 'Comparando...' : 'Comparar' }}
      </button>
    </div>

    <div class="mt-4 max-h-64 overflow-auto rounded-md border bg-gray-50 p-3">
      <p v-if="results.length === 0" class="text-sm text-gray-500">
        Nenhum resultado de comparacao disponivel.
      </p>

      <div
        v-for="(item, index) in results"
        :key="`result-${index}`"
        class="mb-2 rounded border bg-white p-2 text-sm text-gray-700"
      >
        {{ formatResult(item) }}
      </div>
    </div>
  </section>
</template>
