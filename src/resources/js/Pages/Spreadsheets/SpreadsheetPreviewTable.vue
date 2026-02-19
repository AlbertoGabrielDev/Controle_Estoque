<script setup>
import { computed, reactive, ref, watch } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  rows: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const sortColumn = ref('')
const sortDirection = ref('asc')
const visibleColumns = reactive({})

const columns = computed(() => {
  const firstRow = props.rows?.[0]
  if (!firstRow) {
    return []
  }

  if (Array.isArray(firstRow)) {
    return firstRow.map((_, index) => String(index))
  }

  if (typeof firstRow === 'object') {
    return Object.keys(firstRow)
  }

  return ['value']
})

watch(
  columns,
  (nextColumns) => {
    Object.keys(visibleColumns).forEach((key) => {
      if (!nextColumns.includes(key)) {
        delete visibleColumns[key]
      }
    })

    nextColumns.forEach((column) => {
      if (!(column in visibleColumns)) {
        visibleColumns[column] = true
      }
    })

    if (sortColumn.value && !nextColumns.includes(sortColumn.value)) {
      sortColumn.value = ''
      sortDirection.value = 'asc'
    }
  },
  { immediate: true }
)

const displayedColumns = computed(() => columns.value.filter((column) => visibleColumns[column]))

const sortedRows = computed(() => {
  const clonedRows = [...(props.rows ?? [])]
  if (!sortColumn.value) {
    return clonedRows
  }

  return clonedRows.sort((leftRow, rightRow) => compareRows(leftRow, rightRow, sortColumn.value, sortDirection.value))
})

function compareRows(leftRow, rightRow, column, direction) {
  const leftRaw = normalizeCellValue(leftRow, column)
  const rightRaw = normalizeCellValue(rightRow, column)

  const leftNumber = Number(leftRaw)
  const rightNumber = Number(rightRaw)

  let result = 0
  if (!Number.isNaN(leftNumber) && !Number.isNaN(rightNumber)) {
    result = leftNumber - rightNumber
  } else {
    result = String(leftRaw).localeCompare(String(rightRaw), 'pt-BR', { sensitivity: 'base' })
  }

  return direction === 'asc' ? result : result * -1
}

function normalizeCellValue(row, column) {
  if (Array.isArray(row)) {
    return row[Number(column)] ?? ''
  }

  if (typeof row === 'object' && row !== null) {
    return row[column] ?? ''
  }

  return row ?? ''
}

function toggleSort(column) {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    return
  }

  sortColumn.value = column
  sortDirection.value = 'asc'
}

function sortIndicator(column) {
  if (sortColumn.value !== column) {
    return ''
  }

  return sortDirection.value === 'asc' ? '↑' : '↓'
}
</script>

<template>
  <section class="rounded-lg border bg-white p-4">
    <div class="mb-3 flex items-center justify-between gap-3">
      <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>

      <details class="relative">
        <summary class="cursor-pointer rounded-md border bg-gray-50 px-3 py-1 text-sm text-gray-700">
          Colunas
        </summary>
        <div class="absolute right-0 z-10 mt-2 w-52 max-h-56 overflow-auto rounded-md border bg-white p-2 shadow">
          <label
            v-for="column in columns"
            :key="`control-${column}`"
            class="mb-1 flex items-center gap-2 text-sm text-gray-700"
          >
            <input v-model="visibleColumns[column]" type="checkbox">
            <span>{{ column }}</span>
          </label>
        </div>
      </details>
    </div>

    <div v-if="loading" class="rounded-md border bg-gray-50 p-6 text-center text-gray-500">
      Carregando dados da planilha...
    </div>

    <div v-else-if="rows.length === 0" class="rounded-md border bg-gray-50 p-6 text-center text-gray-500">
      Nenhum dado disponivel para esta planilha.
    </div>

    <div v-else class="max-h-[26rem] overflow-auto border rounded-md">
      <table class="min-w-full border-collapse text-sm">
        <thead class="bg-gray-100 sticky top-0">
          <tr>
            <th
              v-for="column in displayedColumns"
              :key="`head-${column}`"
              class="cursor-pointer border px-3 py-2 text-left text-xs font-semibold uppercase text-gray-600"
              @click="toggleSort(column)"
            >
              {{ column }} {{ sortIndicator(column) }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(row, rowIndex) in sortedRows"
            :key="`row-${rowIndex}`"
            class="odd:bg-white even:bg-gray-50"
          >
            <td
              v-for="column in displayedColumns"
              :key="`cell-${rowIndex}-${column}`"
              class="border px-3 py-2 text-gray-700 whitespace-nowrap"
            >
              {{ normalizeCellValue(row, column) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>
