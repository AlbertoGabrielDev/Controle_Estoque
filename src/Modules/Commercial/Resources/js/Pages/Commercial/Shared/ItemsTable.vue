<script setup>
import { useI18n } from 'vue-i18n'

const props = defineProps({
  items: { type: Array, default: () => [] },
  qtyField: { type: String, default: 'quantidade' },
  invoicedField: { type: String, default: 'quantidade_faturada' },
  showInvoiced: { type: Boolean, default: false },
  amountField: { type: String, default: 'total_linha' },
})
const { t } = useI18n()

/**
 * Resolve numeric value from the provided row field.
 *
 * @param {Record<string, any>} row
 * @param {string} field
 * @returns {number}
 */
function num(row, field) {
  return Number(row?.[field] ?? 0)
}
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-slate-700 dark:text-slate-200">
      <thead class="bg-slate-50 dark:bg-slate-800/70">
        <tr>
          <th class="px-3 py-2 text-left">{{ t('Item') }}</th>
          <th class="px-3 py-2 text-left">{{ t('Description') }}</th>
          <th class="px-3 py-2 text-right">{{ t('Qty') }}</th>
          <th v-if="showInvoiced" class="px-3 py-2 text-right">{{ t('Invoiced') }}</th>
          <th class="px-3 py-2 text-right">{{ t('Unit Price') }}</th>
          <th class="px-3 py-2 text-right">{{ t('Line Total') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id" class="border-t dark:border-slate-700">
          <td class="px-3 py-2">{{ item.item?.sku || item.item_id }}</td>
          <td class="px-3 py-2">{{ item.descricao_snapshot }}</td>
          <td class="px-3 py-2 text-right">{{ num(item, qtyField).toFixed(3) }}</td>
          <td v-if="showInvoiced" class="px-3 py-2 text-right">{{ num(item, invoicedField).toFixed(3) }}</td>
          <td class="px-3 py-2 text-right">R$ {{ num(item, 'preco_unit').toFixed(2) }}</td>
          <td class="px-3 py-2 text-right font-medium">R$ {{ num(item, amountField).toFixed(2) }}</td>
        </tr>
        <tr v-if="!items.length">
          <td :colspan="showInvoiced ? 6 : 5" class="px-3 py-3 text-center text-slate-500 dark:text-slate-400">{{ t('No items.') }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
