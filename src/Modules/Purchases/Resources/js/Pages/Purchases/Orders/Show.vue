<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  order: { type: Object, required: true },
})
</script>

<template>
  <Head title="Pedido" />

  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
      <h2 class="text-2xl font-semibold">Pedido {{ props.order.numero }}</h2>
      <div class="text-sm text-slate-600">Status: {{ props.order.status }}</div>
    </div>
    <div class="flex flex-wrap gap-2">
      <Link :href="route('purchases.orders.index')" class="text-blue-600">Voltar</Link>
      <Link
        v-if="props.order.status !== 'cancelado' && props.order.status !== 'fechado'"
        method="patch"
        as="button"
        :href="route('purchases.orders.cancel', props.order.id)"
        class="px-3 py-1 rounded bg-red-600 text-white"
      >
        Cancelar
      </Link>
      <Link
        v-if="props.order.status === 'recebido'"
        method="patch"
        as="button"
        :href="route('purchases.orders.close', props.order.id)"
        class="px-3 py-1 rounded bg-slate-700 text-white"
      >
        Fechar
      </Link>
    </div>
  </div>

  <div class="bg-white rounded shadow p-4 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <div class="text-xs text-slate-500">Fornecedor</div>
        <div class="font-medium">{{ props.order.supplier?.nome_fornecedor ?? props.order.supplier_id }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Data Emissao</div>
        <div class="font-medium">{{ props.order.data_emissao }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Total</div>
        <div class="font-medium">{{ props.order.total }}</div>
      </div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Itens</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm border">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 text-left">Item ID</th>
              <th class="px-3 py-2 text-left">Descricao</th>
              <th class="px-3 py-2 text-left">Qtd. Pedida</th>
              <th class="px-3 py-2 text-left">Qtd. Recebida</th>
              <th class="px-3 py-2 text-left">Preco Unit</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in props.order.items" :key="item.id" class="border-t">
              <td class="px-3 py-2">{{ item.item_id }}</td>
              <td class="px-3 py-2">{{ item.descricao_snapshot }}</td>
              <td class="px-3 py-2">{{ item.quantidade_pedida }}</td>
              <td class="px-3 py-2">{{ item.quantidade_recebida }}</td>
              <td class="px-3 py-2">{{ item.preco_unit }}</td>
            </tr>
            <tr v-if="!props.order.items?.length">
              <td colspan="5" class="px-3 py-3 text-center text-slate-500">Nenhum item.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Recebimentos</h3>
      <div v-if="props.order.receipts?.length" class="flex flex-wrap gap-2">
        <Link
          v-for="receipt in props.order.receipts"
          :key="receipt.id"
          :href="route('purchases.receipts.show', receipt.id)"
          class="px-2 py-1 rounded bg-slate-100 text-slate-700"
        >
          {{ receipt.numero }} ({{ receipt.status }})
        </Link>
      </div>
      <div v-else class="text-sm text-slate-500">Nenhum recebimento vinculado.</div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Devolucoes</h3>
      <div v-if="props.order.returns?.length" class="flex flex-wrap gap-2">
        <Link
          v-for="purchaseReturn in props.order.returns"
          :key="purchaseReturn.id"
          :href="route('purchases.returns.show', purchaseReturn.id)"
          class="px-2 py-1 rounded bg-slate-100 text-slate-700"
        >
          {{ purchaseReturn.numero }} ({{ purchaseReturn.status }})
        </Link>
      </div>
      <div v-else class="text-sm text-slate-500">Nenhuma devolucao vinculada.</div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Contas a Pagar</h3>
      <div v-if="props.order.payables?.length" class="flex flex-wrap gap-2">
        <Link
          v-for="payable in props.order.payables"
          :key="payable.id"
          :href="route('purchases.payables.show', payable.id)"
          class="px-2 py-1 rounded bg-slate-100 text-slate-700"
        >
          {{ payable.numero_documento }} ({{ payable.status }})
        </Link>
      </div>
      <div v-else class="text-sm text-slate-500">Nenhuma conta vinculada.</div>
    </div>
  </div>
</template>
