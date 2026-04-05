<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  invoice_id: { type: Number, default: null },
  returnable_items: { type: Array, default: () => [] },
})

const form = useForm({
  invoice_id: props.invoice_id ?? '',
  order_id: '',
  cliente_id: '',
  motivo: '',
  data_devolucao: new Date().toISOString().split('T')[0],
  items: props.returnable_items.map(i => ({
    invoice_item_id: i.id,
    order_item_id: i.order_item_id ?? '',
    item_id: i.item_id,
    descricao_snapshot: i.descricao_snapshot,
    quantidade_devolvida: Number(i.quantidade_faturada),
    observacoes: '',
    _selected: true,
  })),
})

function addItem() {
  form.items.push({ invoice_item_id: '', order_item_id: '', item_id: '', descricao_snapshot: '', quantidade_devolvida: 1, observacoes: '', _selected: true })
}
function removeItem(i) { form.items.splice(i, 1) }

function submit() {
  const payload = { ...form.data(), items: form.items.filter(i => i._selected).map(({ _selected, descricao_snapshot, ...rest }) => rest) }
  form.transform(() => payload).post(route('commercial.returns.store'))
}
</script>

<template>
  <Head :title="$t('New Return')" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('New Return') }}</h1>
    <Link :href="route('commercial.returns.index')" class="text-blue-600 dark:text-blue-400">{{ $t('Back') }}</Link>
  </div>

  <form @submit.prevent="submit" class="bg-white p-4 rounded shadow dark:bg-slate-900 dark:border dark:border-slate-700 space-y-4 text-slate-700 dark:text-slate-100">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Customer ID') }} *</label>
        <input v-model="form.cliente_id" type="number" min="1" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" required>
        <div v-if="form.errors.cliente_id" class="text-red-600 text-sm mt-1">{{ form.errors.cliente_id }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Return Date') }}</label>
        <input v-model="form.data_devolucao" type="date" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium">{{ $t('Reason') }} *</label>
        <textarea v-model="form.motivo" rows="2" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" required></textarea>
        <div v-if="form.errors.motivo" class="text-red-600 text-sm mt-1">{{ form.errors.motivo }}</div>
      </div>
    </div>

    <div class="flex items-center justify-between">
      <h3 class="font-semibold">{{ $t('Items') }}</h3>
      <button type="button" @click="addItem" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:text-slate-100 text-sm">{{ $t('Add Item') }}</button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm border dark:border-slate-700 text-slate-700 dark:text-slate-200">
        <thead class="bg-slate-50 dark:bg-slate-800/70"><tr>
          <th class="px-3 py-2 w-10"></th>
          <th class="px-3 py-2 text-left">{{ $t('Description') }}</th>
          <th class="px-3 py-2 text-left">{{ $t('Item ID') }}</th>
          <th class="px-3 py-2 text-left">{{ $t('Returned Qty') }}</th>
          <th class="px-3 py-2 text-left">{{ $t('Notes') }}</th>
          <th class="px-3 py-2 w-10"></th>
        </tr></thead>
        <tbody>
          <tr v-for="(item, index) in form.items" :key="index" class="border-t dark:border-slate-700">
            <td class="px-3 py-2 text-center"><input type="checkbox" v-model="item._selected" class="rounded"></td>
            <td class="px-3 py-2">{{ item.descricao_snapshot || $t('N/A') }}</td>
            <td class="px-3 py-2"><input v-model="item.item_id" type="number" min="1" class="border rounded px-2 py-1 w-24 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" :disabled="!!item.invoice_item_id"></td>
            <td class="px-3 py-2"><input v-model="item.quantidade_devolvida" type="number" step="0.001" min="0.001" class="border rounded px-2 py-1 w-28 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100"></td>
            <td class="px-3 py-2"><input v-model="item.observacoes" class="border rounded px-2 py-1 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100"></td>
            <td class="px-3 py-2"><button type="button" @click="removeItem(index)" class="px-2 py-1 rounded bg-red-50 text-red-600 dark:bg-red-900/40 dark:text-red-300 text-xs">×</button></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex justify-end">
      <button :disabled="form.processing" type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">{{ $t('Register Return') }}</button>
    </div>
  </form>
</template>
