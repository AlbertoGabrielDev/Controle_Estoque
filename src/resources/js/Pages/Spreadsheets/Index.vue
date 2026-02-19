<script setup>
import axios from 'axios'
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import ComparisonPanel from './ComparisonPanel.vue'
import FileInputCard from './FileInputCard.vue'
import SpreadsheetPreviewTable from './SpreadsheetPreviewTable.vue'

const props = defineProps({
  maxPreviewRows: {
    type: Number,
    default: 10000,
  },
  maxUploadSizeMb: {
    type: Number,
    default: 20,
  },
  operators: {
    type: Array,
    default: () => [],
  },
})

const fileOne = ref(null)
const fileTwo = ref(null)
const fileOneToken = ref('')
const fileTwoToken = ref('')

const rowsOne = ref([])
const rowsTwo = ref([])
const comparisonResults = ref([])

const uploading = ref(false)
const loadingOne = ref(false)
const loadingTwo = ref(false)
const comparing = ref(false)

const defaultOperators = computed(() => {
  if (props.operators?.length) {
    return props.operators
  }

  return [
    { value: 'igual', label: 'Igual' },
    { value: 'maior', label: 'Maior que' },
    { value: 'menor', label: 'Menor que' },
    { value: 'diferenca', label: 'Diferenca de valor' },
  ]
})

const columnsOne = computed(() => extractColumns(rowsOne.value))
const columnsTwo = computed(() => extractColumns(rowsTwo.value))

function onFileOneSelected(file) {
  fileOne.value = file
}

function onFileTwoSelected(file) {
  fileTwo.value = file
}

async function uploadFiles() {
  if (!fileOne.value || !fileTwo.value) {
    notify('Selecione os dois arquivos para envio.', 'warning')
    return
  }

  const formData = new FormData()
  formData.append('file1', fileOne.value)
  formData.append('file2', fileTwo.value)

  uploading.value = true
  try {
    const { data } = await axios.post(route('spreadsheet.upload'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    fileOneToken.value = extractToken(data?.file1)
    fileTwoToken.value = extractToken(data?.file2)

    await Promise.all([
      loadPreview(1, fileOneToken.value),
      loadPreview(2, fileTwoToken.value),
    ])

    comparisonResults.value = []
    notify(data?.message || 'Arquivos enviados com sucesso.', 'success')
  } catch (error) {
    notify(error?.response?.data?.message || 'Falha ao enviar arquivos.', 'error')
  } finally {
    uploading.value = false
  }
}

async function loadPreview(sheetNumber, fileToken) {
  if (!fileToken) {
    return
  }

  if (sheetNumber === 1) {
    loadingOne.value = true
  } else {
    loadingTwo.value = true
  }

  try {
    const { data } = await axios.get(route('spreadsheet.data', { filename: fileToken }))
    if (sheetNumber === 1) {
      rowsOne.value = Array.isArray(data) ? data : []
    } else {
      rowsTwo.value = Array.isArray(data) ? data : []
    }
  } catch (_) {
    notify(`Falha ao carregar dados da planilha ${sheetNumber}.`, 'error')
    if (sheetNumber === 1) {
      rowsOne.value = []
    } else {
      rowsTwo.value = []
    }
  } finally {
    if (sheetNumber === 1) {
      loadingOne.value = false
    } else {
      loadingTwo.value = false
    }
  }
}

async function compare(payload) {
  if (!fileOneToken.value || !fileTwoToken.value) {
    notify('Envie os arquivos antes da comparacao.', 'warning')
    return
  }

  comparing.value = true
  try {
    const { data } = await axios.post(route('spreadsheet.compare'), {
      file1: fileOneToken.value,
      file2: fileTwoToken.value,
      coluna1: payload.coluna1,
      coluna2: payload.coluna2,
      operador: payload.operador,
    })

    comparisonResults.value = Array.isArray(data) ? data : []
  } catch (error) {
    comparisonResults.value = []
    notify(error?.response?.data?.message || 'Falha na comparacao das planilhas.', 'error')
  } finally {
    comparing.value = false
  }
}

function extractColumns(rows) {
  const firstRow = rows?.[0]
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
}

function extractToken(path) {
  const parts = String(path ?? '').split('/')
  return parts[parts.length - 1] || ''
}

function notify(message, type = 'success') {
  if (typeof window !== 'undefined' && typeof window.showToast === 'function') {
    window.showToast(message, type)
  }
}
</script>

<template>
  <Head title="Comparador de Planilhas" />

  <div class="space-y-6 max-w-7xl mx-auto">
    <header class="rounded-lg bg-white p-4 shadow">
      <h1 class="text-2xl font-bold text-gray-800">Comparador de Planilhas</h1>
      <p class="mt-1 text-sm text-gray-500">
        Upload duplo com preview de ate {{ maxPreviewRows }} linhas e limite de {{ maxUploadSizeMb }}MB por arquivo.
      </p>
    </header>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <FileInputCard
        title="Planilha 1"
        :file-name="fileOne?.name || ''"
        :disabled="uploading"
        @select="onFileOneSelected"
      />
      <FileInputCard
        title="Planilha 2"
        :file-name="fileTwo?.name || ''"
        :disabled="uploading"
        @select="onFileTwoSelected"
      />
    </section>

    <button
      type="button"
      class="rounded-md bg-green-600 px-4 py-2 text-white font-medium hover:bg-green-700 disabled:opacity-60"
      :disabled="uploading"
      @click="uploadFiles"
    >
      {{ uploading ? 'Enviando...' : 'Enviar arquivos' }}
    </button>

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-4">
      <SpreadsheetPreviewTable
        title="Preview Planilha 1"
        :rows="rowsOne"
        :loading="loadingOne"
      />
      <SpreadsheetPreviewTable
        title="Preview Planilha 2"
        :rows="rowsTwo"
        :loading="loadingTwo"
      />
    </section>

    <ComparisonPanel
      :columns-one="columnsOne"
      :columns-two="columnsTwo"
      :operators="defaultOperators"
      :results="comparisonResults"
      :loading="comparing"
      @compare="compare"
    />
  </div>
</template>
