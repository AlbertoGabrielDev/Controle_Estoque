<!-- resources/js/Components/DataTable.vue -->
<script setup>
import { onMounted, onBeforeUnmount, ref, watch, nextTick } from 'vue'
import 'datatables.net-dt'
import 'datatables.net-responsive-dt'
import 'datatables.net-dt/css/dataTables.dataTables.css'

const props = defineProps({
  tableId: { type: String, required: true },

  // serverSide (ajax) vs enhanceOnly
  enhanceOnly: { type: Boolean, default: false },
  ajaxUrl: { type: String, default: '' },
  ajaxParams: { type: Object, default: () => ({}) },

  columns: { type: Array, default: () => [] },
  columnDefs: { type: Array, default: () => [] },

  order: { type: Array, default: () => [[0,'asc']] },
  pageLength: { type: Number, default: 10 },
  lengthMenu: { type: Array, default: () => [[10,25,50,100],[10,25,50,100]] },
  responsive: { type: Boolean, default: false },
  dom: { type: String, default: '<"flex justify-between items-center mb-4"lfr>t<"flex justify-between items-center mt-4"ip>' },
  languageUrl: { type: String, default: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },

  // reinitKey para forçar reinicialização
  reinitKey: { type: [String, Number], default: 0 },
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
    columnDefs: [
      // padding padronizado (igual sua tabela)
      { targets: '_all', createdCell: (td) => td.classList.add('px-4','py-3') },
      ...props.columnDefs
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
}

function destroy(){ try{ if(dt){ dt.destroy(); dt=null } }catch{} }

onMounted(async ()=>{ await nextTick(); init() })
onBeforeUnmount(()=> destroy())

// Reinicializa quando filtros mudarem
watch(()=> JSON.stringify(props.ajaxParams), async ()=>{
  if (dt && !props.enhanceOnly) {
    dt.ajax.reload(null,false); // reload mantendo página
  } else {
    destroy(); await nextTick(); init();
  }
})

watch(()=> props.reinitKey, async ()=>{ destroy(); await nextTick(); init() })
</script>

<template>
  <div class="overflow-x-auto rounded-lg border">
    <table :id="tableId" ref="tableRef" class="w-full">
      <thead class="bg-gray-50">
        <tr><slot name="thead" /></tr>
      </thead>
      <tbody v-if="enhanceOnly" class="divide-y divide-gray-200">
        <slot name="tbody" />
      </tbody>
    </table>
  </div>
</template>
