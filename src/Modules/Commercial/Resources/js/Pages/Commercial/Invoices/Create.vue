<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, reactive } from 'vue'

const props = defineProps({
  order: { type: Object, required: true },
  invoiceable_items: { type: Array, default: () => [] },
})

const form = useForm({
  order_id: props.order.id,
  data_emissao: new Date().toISOString().split('T')[0],
  data_vencimento: '',
  observacoes: '',
  items: props.invoiceable_items.map(oi => ({
    order_item_id: oi.id,
    item_id: oi.item_id,
    descricao_snapshot: oi.descricao_snapshot,
    quantidade_faturada: Number(oi.quantidade) - Number(oi.quantidade_faturada),
    preco_unit: oi.preco_unit,
    desconto_percent: oi.desconto_percent ?? 0,
    desconto_valor: oi.desconto_valor ?? 0,
    imposto_id: oi.imposto_id ?? '',
    aliquota_snapshot: oi.aliquota_snapshot ?? 0,
    _selected: true,
  })),
})

const selectedItems = computed(() => form.items.filter(i => i._selected))

const total = computed(() => selectedItems.value.reduce((s, i) => {
  const base = Number(i.preco_unit) * Number(i.quantidade_faturada)
  const desc = Number(i.desconto_valor)
  const aliq = Number(i.aliquota_snapshot)
  const sub = base - desc
  return s + Math.round((sub + sub * aliq / 100) * 100) / 100
}, 0))

function submit() {
  const payload = { ...form.data(), items: selectedItems.value.map(({ _selected, ...rest }) => rest) }
  form.transform(() => payload).post(route('commercial.invoices.store'))
}
</script>

<template>
  <Head :title="$t('Issue Invoice')" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('Issue Invoice') }} — {{ $t('Sales Order') }} {{ order.numero }}</h1>
    <Link :href="route('commercial.orders.show', order.id)" class="text-blue-600 dark:text-blue-400">{{ $t('Back') }}</Link>
  </div>

  <form @submit.prevent="submit" class="bg-white p-4 rounded shadow dark:bg-slate-900 dark:border dark:border-slate-700 space-y-4 text-slate-700 dark:text-slate-100">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Issue Date') }}</label>
        <input v-model="form.data_emissao" type="date" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Due Date') }}</label>
        <input v-model="form.data_vencimento" type="date" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium">{{ $t('Notes') }}</label>
        <textarea v-model="form.observacoes" rows="2" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100"></textarea>
      </div>
    </div>

    <h3 class="font-semibold">{{ $t('Items to Invoice') }}</h3>
    <div class="overflow-x-auto">
      <table class="w-full text-sm border dark:border-slate-700 text-slate-700 dark:text-slate-200">
        <thead class="bg-slate-50 dark:bg-slate-800/70"><tr>
          <th class="px-3 py-2 w-10"></th>
          <th class="px-3 py-2 text-left">{{ $t('Description') }}</th>
          <th class="px-3 py-2 text-right">{{ $t('Balance') }}</th>
          <th class="px-3 py-2 text-right">{{ $t('Qty to Invoice') }}</th>
          <th class="px-3 py-2 text-right">{{ $t('Unit Price') }}</th>
          <th class="px-3 py-2 text-right">{{ $t('Total') }}</th>
        </tr></thead>
        <tbody>
          <tr v-for="(item, index) in form.items" :key="index" class="border-t dark:border-slate-700">
            <td class="px-3 py-2 text-center"><input type="checkbox" v-model="item._selected" class="rounded"></td>
            <td class="px-3 py-2">{{ item.descricao_snapshot }}</td>
            <td class="px-3 py-2 text-right">{{ Number(item.quantidade_faturada).toFixed(3) }}</td>
            <td class="px-3 py-2"><input v-model="item.quantidade_faturada" type="number" step="0.001" min="0.001" :max="item.quantidade_faturada" class="border rounded px-2 py-1 w-24 text-right dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" :disabled="!item._selected"></td>
            <td class="px-3 py-2 text-right">R$ {{ Number(item.preco_unit).toFixed(2) }}</td>
            <td class="px-3 py-2 text-right font-medium">R$ {{ (Number(item.preco_unit) * Number(item.quantidade_faturada)).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex justify-between items-center">
      <span class="font-semibold">{{ $t('Invoice Total') }}: R$ {{ total.toFixed(2) }}</span>
      <button :disabled="form.processing || selectedItems.length === 0" type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">{{ $t('Issue Invoice') }}</button>
    </div>
  </form>
</template>
