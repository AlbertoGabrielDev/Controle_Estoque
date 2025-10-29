
<script>
export const esc = (s) =>
  String(s ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;')

export function linkify(
  col,
  { routeName, idField = 'id', className = 'text-blue-600 hover:underline' } = {}
) {
  const originalRender = col.render
  col.render = (data, type, row) => {
    const base = originalRender ? originalRender(data, type, row) : data
    if (type !== 'display') return base
    const url =
      typeof route === 'function' && routeName
        ? route(routeName, row?.[idField])
        : `/${String(routeName || '').replace('.', '/')}/${row?.[idField]}`

    return `<a href="${url}" class="${className}">${esc(base ?? '—')}</a>`
  }
  return col
}
</script>
<script setup>
import { onMounted, onBeforeUnmount, ref, watch, nextTick } from 'vue'
import 'datatables.net-dt'
import 'datatables.net-responsive-dt'
import 'datatables.net-dt/css/dataTables.dataTables.css'

const props = defineProps({
  tableId: { type: String, required: true },
  enhanceOnly: { type: Boolean, default: false },
  ajaxUrl: { type: String, default: '' },
  ajaxParams: { type: Object, default: () => ({}) },
  columns: { type: Array, default: () => [] },
  columnDefs: { type: Array, default: () => [] },
  order: { type: Array, default: () => [[0, 'asc']] },
  pageLength: { type: Number, default: 10 },
  lengthMenu: { type: Array, default: () => [[10, 25, 50, 100], [10, 25, 50, 100]] },
  responsive: { type: Boolean, default: false },
  dom: { type: String, default: '<"flex justify-between items-center mb-4"lfr>t<"flex justify-between items-center mt-4"ip>' },
  languageUrl: { type: String, default: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
  reinitKey: { type: [String, Number], default: 0 },
  actionsColIndex: { type: Number, default: -1 },

  paging: { type: Boolean, default: true },         // liga/desliga paginação
  info: { type: Boolean, default: true },           // "Mostrando 1 a 10 de…"
  lengthChange: { type: Boolean, default: true },   // seletor 10/25/50/100
  searching: { type: Boolean, default: true },      // busca global
  ordering: { type: Boolean, default: true },       // ordenação
})

const tableRef = ref(null)
let dt = null

function init() {
  const $table = window.jQuery(tableRef.value)
  if (!$table || !$table.DataTable) return

  const common = {
    processing: true,
    autoWidth: false,
    responsive: props.responsive ? { details: false } : false,
    language: { url: props.languageUrl },
    dom: props.dom,
    order: props.order,
    pageLength: props.pageLength,
    lengthMenu: props.lengthMenu,
    paging: props.paging,
    info: props.info,
    lengthChange: props.lengthChange,
    searching: props.searching,
    ordering: props.ordering,
    pagingType: 'full_numbers',
    headerCallback: (thead) => {
      window.jQuery(thead)
        .find('th')
        .addClass('px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50')
    },
    createdRow: (row) => {
      window.jQuery(row).find('td, th').addClass('px-4 py-3 text-sm text-gray-700')
      window.jQuery(row).addClass('hover:bg-gray-50')
    },
    columnDefs: [
      { targets: '_all', createdCell: (td) => td.classList.add('px-4', 'py-3') },
      ...(props.columnDefs || []),
    ],
  }

  if (!props.enhanceOnly && props.ajaxUrl) {
    dt = $table.DataTable({
      ...common,
      serverSide: true,
      searching: true,
      ajax: {
        url: props.ajaxUrl,
        type: 'GET',
        data: (d) => Object.assign(d, props.ajaxParams || {}),
        error: (xhr) => console.error('DT Ajax error:', xhr.status, xhr.responseText),
      },
      columns: props.columns,
    })
  } else {
    dt = $table.DataTable({
      ...common,
      serverSide: false,
      searching: true,
    })
  }

  if (props.actionsColIndex >= 0) {
    dt.on('draw.dt', () => {
      window.jQuery(dt.column(props.actionsColIndex).nodes()).addClass('text-left')
    })
  }
}

function destroy() {
  try {
    if (dt) {
      dt.destroy()
      dt = null
    }
  } catch {}
}

onMounted(async () => {
  await nextTick()
  init()
})
onBeforeUnmount(() => destroy())

watch(() => JSON.stringify(props.ajaxParams), async () => {
  if (dt && !props.enhanceOnly) dt.ajax.reload(null, false)
  else {
    destroy()
    await nextTick()
    init()
  }
})

watch(() => props.reinitKey, async () => {
  destroy()
  await nextTick()
  init()
})
</script>

<template>
  <div class="dt-tailwind overflow-x-auto rounded-lg border">
    <table :id="tableId" ref="tableRef" class="w-full">
      <thead class="bg-gray-50">
        <tr>
          <slot name="thead" />
        </tr>
      </thead>
      <tbody v-if="enhanceOnly" class="divide-y divide-gray-200">
        <slot name="tbody" />
      </tbody>
    </table>
  </div>
</template>
