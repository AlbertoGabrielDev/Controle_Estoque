<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  payable: { type: Object, required: true },
})
</script>

<template>
  <Head title="Conta a Pagar" />

  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
      <h2 class="text-2xl font-semibold">Documento {{ props.payable.numero_documento }}</h2>
      <div class="text-sm text-slate-600">Status: {{ props.payable.status }}</div>
    </div>
    <Link :href="route('purchases.payables.index')" class="text-blue-600">Voltar</Link>
  </div>

  <div class="bg-white rounded shadow p-4 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <div class="text-xs text-slate-500">Fornecedor</div>
        <div class="font-medium">{{ props.payable.supplier?.nome_fornecedor ?? props.payable.supplier_id }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Data Emissao</div>
        <div class="font-medium">{{ props.payable.data_emissao }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Data Vencimento</div>
        <div class="font-medium">{{ props.payable.data_vencimento }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <div class="text-xs text-slate-500">Pedido</div>
        <div class="font-medium">
          <Link
            v-if="props.payable.order_id"
            :href="route('purchases.orders.show', props.payable.order_id)"
            class="text-blue-600"
          >
            {{ props.payable.order_id }}
          </Link>
          <span v-else>-</span>
        </div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Recebimento</div>
        <div class="font-medium">
          <Link
            v-if="props.payable.receipt_id"
            :href="route('purchases.receipts.show', props.payable.receipt_id)"
            class="text-blue-600"
          >
            {{ props.payable.receipt_id }}
          </Link>
          <span v-else>-</span>
        </div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Valor</div>
        <div class="font-medium">{{ props.payable.valor_total }}</div>
      </div>
    </div>
  </div>
</template>
