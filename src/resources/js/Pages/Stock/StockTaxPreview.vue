<script setup>
const props = defineProps({
  vm: { type: Object, default: null },
})

const money = (value) =>
  Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
</script>

<template>
  <div v-if="!props.vm" class="text-slate-500 dark:text-slate-400">
    Selecione o produto e informe o preço de venda.
  </div>

  <div v-else class="stock-tax-preview text-slate-700 dark:text-slate-200">
    <div class="mb-2">Preço base: <strong>{{ money(props.vm.__totais?.preco_base) }}</strong></div>
    <div class="mb-2">Somente impostos: <strong>{{ money(props.vm.__totais?.total_impostos) }}</strong></div>
    <div class="mb-3">Preço com impostos: <strong>{{ money(props.vm.__totais?.total_com_impostos) }}</strong></div>

    <div
      v-for="(imp, idx) in props.vm.impostos || []"
      :key="idx"
      class="mb-3 rounded border border-slate-300 dark:border-slate-700 p-3"
    >
      <div class="flex items-center justify-between">
        <div class="font-semibold text-slate-800 dark:text-slate-100">
          {{ imp.imposto || imp.codigo || '—' }}
          <span v-if="imp.tax_nome"> - {{ imp.tax_nome }}</span>
        </div>
        <div class="text-right text-slate-700 dark:text-slate-200">Total: <strong>{{ money(imp.total) }}</strong></div>
      </div>

      <div v-if="Array.isArray(imp.linhas) && imp.linhas.length" class="mt-2 overflow-x-auto">
        <table class="stock-tax-preview-table min-w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-800/70 text-slate-500 dark:text-slate-300">
            <tr>
              <th class="text-left pr-4 py-1">Método</th>
              <th class="text-left pr-4 py-1">Base</th>
              <th class="text-left pr-4 py-1">Alíquota/Valor</th>
              <th class="text-right py-1">Imposto</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(linha, j) in imp.linhas"
              :key="j"
              class="border-t border-slate-200 dark:border-slate-700"
            >
              <td class="pr-4 py-1 text-slate-700 dark:text-slate-200">{{ linha.metodo_label || '—' }}</td>
              <td class="pr-4 py-1 text-slate-700 dark:text-slate-200">{{ money(linha.base) }}</td>
              <td class="pr-4 py-1 text-slate-700 dark:text-slate-200">
                <span v-if="Number(linha.metodo) === 2">{{ money(linha.valor_fixo) }}</span>
                <span v-else>{{ Number(linha.aliquota || 0).toFixed(2) }}%</span>
              </td>
              <td class="text-right py-1 text-slate-700 dark:text-slate-200">{{ money(linha.valor) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style>
.stock-tax-preview .stock-tax-preview-table thead th {
  background-color: #f8fafc !important;
  color: #64748b !important;
  border-bottom: 1px solid #e2e8f0 !important;
}

.stock-tax-preview .stock-tax-preview-table tbody td {
  background-color: transparent !important;
  color: #334155 !important;
  border-top: 1px solid #e2e8f0 !important;
}

.stock-tax-preview .stock-tax-preview-table tbody tr:hover td {
  background-color: #f8fafc !important;
}

.dark .stock-tax-preview .stock-tax-preview-table thead th {
  background-color: rgba(30, 41, 59, 0.7) !important;
  color: #cbd5e1 !important;
  border-bottom-color: #334155 !important;
}

.dark .stock-tax-preview .stock-tax-preview-table tbody td {
  background-color: transparent !important;
  color: #e2e8f0 !important;
  border-top-color: #334155 !important;
}

.dark .stock-tax-preview .stock-tax-preview-table tbody tr:hover td {
  background-color: #111827 !important;
}
</style>
