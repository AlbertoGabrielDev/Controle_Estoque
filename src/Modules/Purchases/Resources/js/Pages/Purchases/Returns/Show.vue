<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  purchaseReturn: { type: Object, required: true },
})
</script>

<template>
  <Head title="Devolucao" />

  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
      <h2 class="text-2xl font-semibold">Devolucao {{ props.purchaseReturn.numero }}</h2>
      <div class="text-sm text-slate-600">Status: {{ props.purchaseReturn.status }}</div>
    </div>
    <div class="flex flex-wrap gap-2">
      <Link :href="route('purchases.returns.index')" class="text-blue-600">Voltar</Link>
      <Link
        v-if="props.purchaseReturn.status === 'aberta'"
        method="patch"
        as="button"
        :href="route('purchases.returns.confirm', props.purchaseReturn.id)"
        class="px-3 py-1 rounded bg-green-600 text-white"
      >
        Confirmar
      </Link>
      <Link
        v-if="props.purchaseReturn.status !== 'confirmada'"
        method="patch"
        as="button"
        :href="route('purchases.returns.cancel', props.purchaseReturn.id)"
        class="px-3 py-1 rounded bg-red-600 text-white"
      >
        Cancelar
      </Link>
    </div>
  </div>

  <div class="bg-white rounded shadow p-4 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <div class="text-xs text-slate-500">Pedido</div>
        <div class="font-medium">
          <Link
            v-if="props.purchaseReturn.order_id"
            :href="route('purchases.orders.show', props.purchaseReturn.order_id)"
            class="text-blue-600"
          >
            {{ props.purchaseReturn.order_id }}
          </Link>
          <span v-else>-</span>
        </div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Recebimento</div>
        <div class="font-medium">
          <Link
            v-if="props.purchaseReturn.receipt_id"
            :href="route('purchases.receipts.show', props.purchaseReturn.receipt_id)"
            class="text-blue-600"
          >
            {{ props.purchaseReturn.receipt_id }}
          </Link>
          <span v-else>-</span>
        </div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Data</div>
        <div class="font-medium">{{ props.purchaseReturn.data_devolucao }}</div>
      </div>
    </div>

    <div>
      <div class="text-xs text-slate-500">Motivo</div>
      <div class="font-medium">{{ props.purchaseReturn.motivo }}</div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Itens</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm border">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 text-left">Item ID</th>
              <th class="px-3 py-2 text-left">Receipt Item</th>
              <th class="px-3 py-2 text-left">Order Item</th>
              <th class="px-3 py-2 text-left">Quantidade</th>
              <th class="px-3 py-2 text-left">Observacoes</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in props.purchaseReturn.items" :key="item.id" class="border-t">
              <td class="px-3 py-2">{{ item.item_id }}</td>
              <td class="px-3 py-2">{{ item.receipt_item_id ?? '-' }}</td>
              <td class="px-3 py-2">{{ item.order_item_id ?? '-' }}</td>
              <td class="px-3 py-2">{{ item.quantidade_devolvida }}</td>
              <td class="px-3 py-2">{{ item.observacoes ?? '-' }}</td>
            </tr>
            <tr v-if="!props.purchaseReturn.items?.length">
              <td colspan="5" class="px-3 py-3 text-center text-slate-500">Nenhum item.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
