<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  requisition: { type: Object, required: true },
})
</script>

<template>
  <Head title="Requisicao" />

  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
      <h2 class="text-2xl font-semibold">Requisicao {{ props.requisition.numero }}</h2>
      <div class="text-sm text-slate-600">Status: {{ props.requisition.status }}</div>
    </div>
    <div class="flex flex-wrap gap-2">
      <Link :href="route('purchases.requisitions.index')" class="text-blue-600">Voltar</Link>
      <Link v-if="props.requisition.status === 'draft'" :href="route('purchases.requisitions.edit', props.requisition.id)" class="text-blue-600">Editar</Link>
      <Link
        v-if="props.requisition.status === 'draft'"
        method="patch"
        as="button"
        :href="route('purchases.requisitions.approve', props.requisition.id)"
        class="px-3 py-1 rounded bg-green-600 text-white"
      >
        Aprovar
      </Link>
      <Link
        v-if="props.requisition.status !== 'fechado'"
        method="patch"
        as="button"
        :href="route('purchases.requisitions.cancel', props.requisition.id)"
        class="px-3 py-1 rounded bg-red-600 text-white"
      >
        Cancelar
      </Link>
      <Link
        v-if="props.requisition.status === 'aprovado'"
        method="patch"
        as="button"
        :href="route('purchases.requisitions.close', props.requisition.id)"
        class="px-3 py-1 rounded bg-slate-700 text-white"
      >
        Fechar
      </Link>
    </div>
  </div>

  <div class="bg-white rounded shadow p-4 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <div class="text-xs text-slate-500">Data</div>
        <div class="font-medium">{{ props.requisition.data_requisicao ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Solicitado por</div>
        <div class="font-medium">{{ props.requisition.solicitado_por ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Observacoes</div>
        <div class="font-medium">{{ props.requisition.observacoes ?? '-' }}</div>
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
              <th class="px-3 py-2 text-left">Quantidade</th>
              <th class="px-3 py-2 text-left">Preco Estimado</th>
              <th class="px-3 py-2 text-left">Imposto ID</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in props.requisition.items" :key="item.id" class="border-t">
              <td class="px-3 py-2">{{ item.item_id }}</td>
              <td class="px-3 py-2">{{ item.descricao_snapshot }}</td>
              <td class="px-3 py-2">{{ item.quantidade }}</td>
              <td class="px-3 py-2">{{ item.preco_estimado }}</td>
              <td class="px-3 py-2">{{ item.imposto_id ?? '-' }}</td>
            </tr>
            <tr v-if="!props.requisition.items?.length">
              <td colspan="5" class="px-3 py-3 text-center text-slate-500">Nenhum item cadastrado.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Vinculos do Fluxo</h3>
      <div class="text-sm text-slate-600">Requisicao -> Cotacao -> Pedido -> Recebimento -> Devolucao -> AP</div>
      <div class="mt-3 space-y-3">
        <div v-if="props.requisition.quotations?.length">
          <div class="text-xs text-slate-500">Cotacoes e Pedidos</div>
          <div class="space-y-3">
            <div v-for="quotation in props.requisition.quotations" :key="quotation.id" class="border rounded p-3">
              <div class="flex flex-wrap items-center gap-2">
                <Link
                  :href="route('purchases.quotations.show', quotation.id)"
                  class="px-2 py-1 rounded bg-slate-100 text-slate-700"
                >
                  {{ quotation.numero }} ({{ quotation.status }})
                </Link>
                <span v-if="!quotation.orders?.length" class="text-xs text-slate-500">Sem pedidos</span>
              </div>
              <div v-if="quotation.orders?.length" class="flex flex-wrap gap-2 mt-2">
                <Link
                  v-for="order in quotation.orders"
                  :key="order.id"
                  :href="route('purchases.orders.show', order.id)"
                  class="px-2 py-1 rounded bg-blue-50 text-blue-700"
                >
                  {{ order.numero }} ({{ order.status }})
                </Link>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-sm text-slate-500">Nenhuma cotacao vinculada.</div>
      </div>
    </div>
  </div>
</template>
