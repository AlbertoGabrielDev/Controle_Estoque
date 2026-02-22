
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
  dom: { type: String, default: '<"erp-dt-toolbar flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4"lfr>t<"erp-dt-footer flex flex-col gap-3 md:flex-row md:items-center md:justify-between mt-4"ip>' },
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
        .addClass('erp-dt-head-cell px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide')
    },
    createdRow: (row) => {
      window.jQuery(row).addClass('erp-dt-row transition-colors')
      window.jQuery(row).find('td, th').addClass('erp-dt-cell px-4 py-3 text-sm')
    },
    columnDefs: [
      { targets: '_all', createdCell: (td) => td.classList.add('erp-dt-cell', 'px-4', 'py-3') },
      ...(props.columnDefs || []),
    ],
  }

  if (!props.enhanceOnly && props.ajaxUrl) {
    dt = $table.DataTable({
      ...common,
      serverSide: true,
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

watch(
  () => props.ajaxParams,
  async () => {
    if (dt && !props.enhanceOnly && dt.ajax) {
      dt.ajax.reload(null, false)
      return
    }

    destroy()
    await nextTick()
    init()
  },
  { deep: true }
)

watch(() => props.reinitKey, async () => {
  destroy()
  await nextTick()
  init()
})
</script>

<template>
  <div class="dt-tailwind overflow-x-auto rounded-2xl border border-slate-200/80 bg-white/85 p-3 md:p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
    <table :id="tableId" ref="tableRef" class="w-full text-sm">
      <thead class="bg-slate-50/90 dark:bg-slate-800/70">
        <tr>
          <slot name="thead" />
        </tr>
      </thead>
      <tbody v-if="enhanceOnly" class="divide-y divide-slate-200 dark:divide-slate-700">
        <slot name="tbody" />
      </tbody>
    </table>
  </div>
</template>

<style>
.dt-tailwind {
  border-color: rgba(219, 228, 238, 0.8);
  background-color: rgba(255, 255, 255, 0.88);
}

.dark .dt-tailwind {
  border-color: #334155 !important;
  background-color: rgba(15, 23, 42, 0.72) !important;
}

.dt-tailwind .dt-container,
.dt-tailwind .dataTables_wrapper {
  color: inherit;
}

.dt-tailwind .dt-container .dt-layout-row,
.dt-tailwind .dt-container .dt-length,
.dt-tailwind .dt-container .dt-search,
.dt-tailwind .dt-container .dt-info,
.dt-tailwind .dt-container .dt-paging,
.dt-tailwind .dataTables_wrapper .dataTables_length,
.dt-tailwind .dataTables_wrapper .dataTables_filter,
.dt-tailwind .dataTables_wrapper .dataTables_info,
.dt-tailwind .dataTables_wrapper .dataTables_paginate {
  color: #475569;
}

.dt-tailwind .dt-container .dt-length label,
.dt-tailwind .dt-container .dt-search label,
.dt-tailwind .dataTables_wrapper .dataTables_length label,
.dt-tailwind .dataTables_wrapper .dataTables_filter label {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.4rem;
}

.dark .dt-tailwind .dt-container .dt-layout-row,
.dark .dt-tailwind .dt-container .dt-length,
.dark .dt-tailwind .dt-container .dt-search,
.dark .dt-tailwind .dt-container .dt-info,
.dark .dt-tailwind .dt-container .dt-paging,
.dark .dt-tailwind .dataTables_wrapper .dataTables_length,
.dark .dt-tailwind .dataTables_wrapper .dataTables_filter,
.dark .dt-tailwind .dataTables_wrapper .dataTables_info,
.dark .dt-tailwind .dataTables_wrapper .dataTables_paginate {
  color: #cbd5e1;
}

.dt-tailwind .dt-container .dt-search input,
.dt-tailwind .dt-container .dt-length select.dt-input,
.dt-tailwind .dt-container .dt-paging .dt-paging-button,
.dt-tailwind .dataTables_wrapper .dataTables_filter input,
.dt-tailwind .dataTables_wrapper .dataTables_length select,
.dt-tailwind .dataTables_wrapper .dataTables_paginate .paginate_button {
  border-radius: 0.7rem !important;
  border: 1px solid #cbd5e1 !important;
  background: #ffffff !important;
  color: #334155 !important;
}

.dt-tailwind .dt-container .dt-paging .dt-paging-button.current,
.dt-tailwind .dt-container .dt-paging .dt-paging-button.current:hover,
.dt-tailwind .dataTables_wrapper .dataTables_paginate .paginate_button.current {
  border-color: #0891b2 !important;
  background: #0891b2 !important;
  color: #ffffff !important;
}

.dark .dt-tailwind .dt-container .dt-search input,
.dark .dt-tailwind .dt-container .dt-length select.dt-input,
.dark .dt-tailwind .dt-container .dt-paging .dt-paging-button,
.dark .dt-tailwind .dataTables_wrapper .dataTables_filter input,
.dark .dt-tailwind .dataTables_wrapper .dataTables_length select,
.dark .dt-tailwind .dataTables_wrapper .dataTables_paginate .paginate_button {
  border-color: #475569 !important;
  background: #0b1220 !important;
  color: #e2e8f0 !important;
}

.dark .dt-tailwind .dt-container .dt-paging .dt-paging-button.current,
.dark .dt-tailwind .dt-container .dt-paging .dt-paging-button.current:hover,
.dark .dt-tailwind .dataTables_wrapper .dataTables_paginate .paginate_button.current {
  border-color: #06b6d4 !important;
  background: #0891b2 !important;
  color: #ffffff !important;
}

.dt-tailwind table.dataTable {
  background: transparent;
}

.dt-tailwind table.dataTable > thead > tr > th.erp-dt-head-cell,
.dt-tailwind table.dataTable > thead > tr > td.erp-dt-head-cell {
  border-bottom: 1px solid #dbe4ee !important;
  background-color: #f8fafc !important;
  color: #64748b !important;
}

.dark .dt-tailwind table.dataTable > thead > tr > th.erp-dt-head-cell,
.dark .dt-tailwind table.dataTable > thead > tr > td.erp-dt-head-cell {
  border-bottom-color: #334155 !important;
  background-color: #0f172a !important;
  color: #94a3b8 !important;
}

.dt-tailwind table.dataTable > tbody > tr > th.erp-dt-cell,
.dt-tailwind table.dataTable > tbody > tr > td.erp-dt-cell {
  color: #334155;
  border-top-color: #eef2f7 !important;
  background-color: transparent !important;
}

.dark .dt-tailwind table.dataTable > tbody > tr > th.erp-dt-cell,
.dark .dt-tailwind table.dataTable > tbody > tr > td.erp-dt-cell {
  color: #cbd5e1 !important;
  border-top-color: #1e293b !important;
  background-color: transparent !important;
}

.dt-tailwind table.dataTable.stripe > tbody > tr:nth-child(odd) > *,
.dt-tailwind table.dataTable.display > tbody > tr:nth-child(odd) > * {
  box-shadow: none !important;
}

.dt-tailwind table.dataTable.hover > tbody > tr:hover > *,
.dt-tailwind table.dataTable.display > tbody > tr:hover > *,
.dt-tailwind table.dataTable tbody > tr.erp-dt-row:hover > * {
  box-shadow: inset 0 0 0 9999px rgba(148, 163, 184, 0.08) !important;
}

.dark .dt-tailwind table.dataTable.hover > tbody > tr:hover > *,
.dark .dt-tailwind table.dataTable.display > tbody > tr:hover > *,
.dark .dt-tailwind table.dataTable tbody > tr.erp-dt-row:hover > * {
  box-shadow: inset 0 0 0 9999px rgba(51, 65, 85, 0.35) !important;
}

.dark .dt-tailwind table.dataTable thead th,
.dark .dt-tailwind table.dataTable thead td,
.dark .dt-tailwind table.dataTable tbody td,
.dark .dt-tailwind table.dataTable tbody th {
  color: #cbd5e1;
}

.dark .dt-tailwind .dt-container .dt-paging .dt-paging-button.disabled,
.dark .dt-tailwind .dt-container .dt-paging .dt-paging-button.disabled:hover {
  color: #64748b !important;
}
</style>
