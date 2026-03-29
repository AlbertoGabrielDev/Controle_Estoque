<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  receipt: { type: Object, required: true },
})
</script>

<template>
  <Head title="Recebimento" />

  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
      <h2 class="text-2xl font-semibold">Recebimento {{ props.receipt.numero }}</h2>
      <div class="text-sm text-slate-600 dark:text-slate-300">Status: {{ props.receipt.status }}</div>
    </div>
    <div class="flex flex-wrap gap-2">
      <Link :href="route('purchases.receipts.index')" class="text-blue-600 dark:text-cyan-400">{{ $t('Back') }}</Link>
      <Link
        v-if="props.receipt.status !== 'estornado'"
        method="patch"
        as="button"
        :href="route('purchases.receipts.check', props.receipt.id)"
        class="px-3 py-1 rounded bg-green-600 text-white"
      >
        Conferir
      </Link>
      <Link
        v-if="props.receipt.status === 'com_divergencia'"
        method="patch"
        as="button"
        :href="route('purchases.receipts.acceptDivergence', props.receipt.id)"
        class="px-3 py-1 rounded bg-blue-600 text-white"
      >
        Aceitar divergencia
      </Link>
      <Link
        v-if="props.receipt.status !== 'estornado'"
        method="patch"
        as="button"
        :href="route('purchases.receipts.reverse', props.receipt.id)"
        class="px-3 py-1 rounded bg-red-600 text-white"
      >
        Estornar
      </Link>
    </div>
  </div>

  <div class="bg-white rounded shadow p-4 space-y-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Pedido</div>
        <Link :href="route('purchases.orders.show', props.receipt.order_id)" class="text-blue-600 dark:text-cyan-400">
          {{ props.receipt.order_id }}
        </Link>
      </div>
      <div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Fornecedor</div>
        <div class="font-medium">{{ props.receipt.supplier?.nome_fornecedor ?? props.receipt.supplier_id }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Data</div>
        <div class="font-medium">{{ props.receipt.data_recebimento }}</div>
      </div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Itens</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm border purchases-table dark:border-slate-700">
          <thead class="bg-slate-50 dark:bg-slate-800/70">
            <tr>
              <th class="px-3 py-2 text-left">Item ID</th>
              <th class="px-3 py-2 text-left">Qtd. Recebida</th>
              <th class="px-3 py-2 text-left">Preco Unit</th>
              <th class="px-3 py-2 text-left">Imposto ID</th>
              <th class="px-3 py-2 text-left">Divergencia</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in props.receipt.items" :key="item.id" class="border-t dark:border-slate-700">
              <td class="px-3 py-2">{{ item.item_id }}</td>
              <td class="px-3 py-2">{{ item.quantidade_recebida }}</td>
              <td class="px-3 py-2">{{ item.preco_unit_recebido }}</td>
              <td class="px-3 py-2">{{ item.imposto_id ?? '-' }}</td>
              <td class="px-3 py-2">{{ item.divergencia_flag ? 'Sim' : 'Nao' }}</td>
            </tr>
            <tr v-if="!props.receipt.items?.length">
              <td colspan="5" class="px-3 py-3 text-center text-slate-500 dark:text-slate-400">Nenhum item.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Devolucoes</h3>
      <div v-if="props.receipt.returns?.length" class="flex flex-wrap gap-2">
        <Link
          v-for="purchaseReturn in props.receipt.returns"
          :key="purchaseReturn.id"
          :href="route('purchases.returns.show', purchaseReturn.id)"
          class="px-2 py-1 rounded bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200"
        >
          {{ purchaseReturn.numero }} ({{ purchaseReturn.status }})
        </Link>
      </div>
      <div v-else class="text-sm text-slate-500 dark:text-slate-400">Nenhuma devolucao vinculada.</div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Contas a Pagar</h3>
      <div v-if="props.receipt.payables?.length" class="flex flex-wrap gap-2">
        <Link
          v-for="payable in props.receipt.payables"
          :key="payable.id"
          :href="route('purchases.payables.show', payable.id)"
          class="px-2 py-1 rounded bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200"
        >
          {{ payable.numero_documento }} ({{ payable.status }})
        </Link>
      </div>
      <div v-else class="text-sm text-slate-500 dark:text-slate-400">Nenhuma conta vinculada.</div>
    </div>
  </div>
</template>
