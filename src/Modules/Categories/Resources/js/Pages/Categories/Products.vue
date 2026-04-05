<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import DataTable, { esc, linkify } from '@/components/DataTable.vue'

const props = defineProps({
  categoriaId: {
    type: Number,
    required: true,
  },
  categoria: {
    type: String,
    default: '',
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  produtos: {
    type: Object,
    default: () => ({}),
  },
})

const { t } = useI18n()

const form = reactive({
  q: props.filters?.q ?? '',
  status: props.filters?.status ?? '',
})

function decodeHtmlEntities(value) {
  if (typeof value !== 'string' || !value.includes('&')) return value
  if (typeof document === 'undefined') return value
  const textarea = document.createElement('textarea')
  textarea.innerHTML = value
  return textarea.value
}

function tryJsonParse(value) {
  try {
    return JSON.parse(value)
  } catch {
    return value
  }
}

function unwrapQuoted(value) {
  if (typeof value !== 'string' || value.length < 2) return value
  const first = value[0]
  const last = value[value.length - 1]
  if ((first === '"' && last === '"') || (first === "'" && last === "'")) {
    return value.slice(1, -1)
  }
  return value
}

function normalizeNutrition(value) {
  if (value === null || value === undefined || value === '') {
    return null
  }

  if (typeof value === 'string') {
    let cleaned = decodeHtmlEntities(value).trim()
    let parsed = tryJsonParse(cleaned)
    if (typeof parsed === 'string') {
      const unwrapped = unwrapQuoted(parsed)
      if (unwrapped !== parsed) {
        parsed = tryJsonParse(unwrapped)
      }
    }
    return parsed
  }

  return value
}

function formatNutritionEntry(entry) {
  if (entry === null || entry === undefined || entry === '') return ''

  if (typeof entry === 'string' || typeof entry === 'number') {
    return String(entry)
  }

  if (typeof entry !== 'object') {
    return String(entry)
  }

  const labelText = String(
    entry.label ?? entry.nome ?? entry.chave ?? entry.key ?? entry.nutriente ?? ''
  ).trim()
  const displayLabel = labelText

  const value = entry.valor ?? entry.value ?? entry.quantidade ?? entry.qtd ?? entry.amount
  const unit = entry.unidade ?? entry.unit ?? entry.un

  if (displayLabel && value !== undefined && value !== null && value !== '') {
    return `${displayLabel}: ${value}${unit ? ` ${unit}` : ''}`
  }

  if (displayLabel) {
    return displayLabel
  }

  if (value !== undefined && value !== null) {
    return String(value)
  }

  try {
    return JSON.stringify(entry)
  } catch {
    return String(entry)
  }
}

function formatNutrition(value, { limit, maxLength } = {}) {
  const parsed = normalizeNutrition(value)
  if (parsed === null || parsed === undefined || parsed === '') return '-'

  if (typeof parsed === 'string') {
    if (maxLength && parsed.length > maxLength) {
      return `${parsed.slice(0, maxLength)}...`
    }
    return parsed
  }

  if (Array.isArray(parsed)) {
    if (parsed.length === 0) return '-'
    const items = parsed.map((entry) => formatNutritionEntry(entry)).filter(Boolean)
    if (items.length === 0) return '-'
    const shown = limit ? items.slice(0, limit) : items
    const text = shown.join(', ')
    if (limit && items.length > limit) {
      return `${text}...`
    }
    return text
  }

  if (typeof parsed === 'object') {
    const keys = Object.keys(parsed)
    if (keys.length === 0) return '-'
    if (keys.length === 1 && Object.prototype.hasOwnProperty.call(parsed, 'texto')) {
      return String(parsed.texto ?? '')
    }

    const entries = Object.entries(parsed)
    const shown = limit ? entries.slice(0, limit) : entries
    const text = shown
      .map(([key, val]) => `${key}: ${val}`)
      .join(', ')

    if (limit && entries.length > limit) {
      return `${text}...`
    }

    return text || '-'
  }

  return String(parsed)
}

function nutritionSummary(data) {
  return formatNutrition(data, { limit: 4, maxLength: 120 })
}

function nutritionTitle(data) {
  return formatNutrition(data)
}

const dtColumns = computed(() => [
  { data: 'c1', title: t('Code') },
  linkify({ data: 'c2', title: t('Name') }, { routeName: 'produtos.editar', idField: 'id' }),
  { data: 'c3', title: t('Description'), className: 'hidden lg:table-cell' },
  { data: 'c4', title: t('Unit') },
  {
    data: 'c5',
    title: t('Nutrition'),
    orderable: false,
    searchable: false,
    render: (data, type) => {
      if (type !== 'display') return data
      return `<span class="text-slate-600" title="${esc(nutritionTitle(data))}">${esc(nutritionSummary(data))}</span>`
    },
  },
  {
    data: 'st',
    title: t('Status'),
    render: (data) => data
      ? `<span class="text-green-700">${t('Active')}</span>`
      : `<span class="text-gray-500">${t('Inactive')}</span>`,
  },
  { data: 'acoes', title: t('Actions'), orderable: false, searchable: false },
])
</script>

<template>
  <Head :title="`Produtos da categoria ${categoria || ''}`" />

  <div>
    <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      <h2 class="text-2xl font-semibold text-slate-700">
        Produtos da Categoria: {{ categoria || `#${categoriaId}` }}
      </h2>

      <div class="flex gap-2">
        <Link
          :href="route('categoria.inicio')"
          class="flex items-center rounded-md bg-gray-100 px-4 py-2 text-gray-800 transition-colors hover:bg-gray-200"
        >
          <i class="fas fa-angle-left mr-2"></i>{{ $t('Back') }}
        </Link>
        <Link
          :href="route('produtos.cadastro')"
          class="flex items-center rounded-md bg-gray-100 px-4 py-2 text-gray-800 transition-colors hover:bg-gray-200"
        >
          <i class="fas fa-plus mr-2"></i>{{ $t('Create') }}
        </Link>
      </div>
    </div>

    <div class="mb-6 mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
      <input
        v-model="form.q"
        type="text"
        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
        :placeholder="$t('Search by code, name, description or unit')"
      >
      <select v-model="form.status" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
        <option value="">{{ $t('Status') }}</option>
        <option :value="1">{{ $t('Active') }}</option>
        <option :value="0">{{ $t('Inactive') }}</option>
      </select>
    </div>

    <DataTable
      table-id="dt-categoria-produtos"
      :ajax-url="route('categorias.produto.data', categoriaId)"
      :ajax-params="form"
      :columns="dtColumns"
      :order="[[1, 'asc']]"
      :page-length="15"
      :actions-col-index="6"
    />
  </div>
</template>
