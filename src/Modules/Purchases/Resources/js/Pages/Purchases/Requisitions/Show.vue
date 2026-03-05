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
      <div class="text-sm text-slate-600 dark:text-slate-300">Status: {{ props.requisition.status }}</div>
    </div>
    <div class="flex flex-wrap gap-2">
      <Link :href="route('purchases.requisitions.index')" class="text-blue-600 dark:text-cyan-400">Voltar</Link>
      <Link v-if="props.requisition.status === 'draft'" :href="route('purchases.requisitions.edit', props.requisition.id)" class="text-blue-600 dark:text-cyan-400">Editar</Link>
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
        class="px-3 py-1 rounded bg-slate-700 text-white dark:bg-slate-600 dark:hover:bg-slate-500 transition-colors"
      >
        Fechar
      </Link>
      <Link
        v-if="props.requisition.status === 'aprovado'"
        :href="route('purchases.orders.create', { requisition_id: props.requisition.id })"
        class="px-3 py-1 rounded bg-emerald-600 text-white hover:bg-emerald-700 transition-colors"
      >
        Gerar Pedido
      </Link>
    </div>
  </div>

  <div class="bg-white rounded shadow p-4 space-y-4 dark:bg-slate-900 dark:border dark:border-slate-700">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Data</div>
        <div class="font-medium">{{ props.requisition.data_requisicao ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Solicitado por</div>
        <div class="font-medium">{{ props.requisition.solicitado_por ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Observacoes</div>
        <div class="font-medium">{{ props.requisition.observacoes ?? '-' }}</div>
      </div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Itens</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm border purchases-table dark:border-slate-700">
          <thead class="bg-slate-50 dark:bg-slate-800/70">
            <tr>
              <th class="px-3 py-2 text-left">Item ID</th>
              <th class="px-3 py-2 text-left">Descricao</th>
              <th class="px-3 py-2 text-left">Quantidade</th>
              <th class="px-3 py-2 text-left">Preco Estimado</th>
              <th class="px-3 py-2 text-left">Imposto ID</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in props.requisition.items" :key="item.id" class="border-t dark:border-slate-700">
              <td class="px-3 py-2">{{ item.item_id }}</td>
              <td class="px-3 py-2">{{ item.descricao_snapshot }}</td>
              <td class="px-3 py-2">{{ item.quantidade }}</td>
              <td class="px-3 py-2">{{ item.preco_estimado }}</td>
              <td class="px-3 py-2">{{ item.imposto_id ?? '-' }}</td>
            </tr>
            <tr v-if="!props.requisition.items?.length">
              <td colspan="5" class="px-3 py-3 text-center text-slate-500 dark:text-slate-400">Nenhum item cadastrado.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Vinculos do Fluxo</h3>
      <div class="text-sm text-slate-600 dark:text-slate-300">Requisicao -> Pedido -> Recebimento -> Devolucao -> AP</div>
      <div class="mt-3 space-y-3">
        <div v-if="props.requisition.orders?.length">
          <div class="text-xs text-slate-500 dark:text-slate-400">Pedidos</div>
          <div class="flex flex-wrap gap-2 mt-2">
            <Link
              v-for="order in props.requisition.orders"
              :key="order.id"
              :href="route('purchases.orders.show', order.id)"
              class="px-2 py-1 rounded bg-blue-50 text-blue-700 dark:bg-cyan-900/40 dark:text-cyan-300"
            >
              {{ order.numero }} ({{ order.status }})
            </Link>
          </div>
        </div>
        <div v-if="props.requisition.quotations?.length">
          <div class="text-xs text-slate-500 dark:text-slate-400">Cotacoes (Legacy)</div>
          <div class="space-y-3">
            <div v-for="quotation in props.requisition.quotations" :key="quotation.id" class="border rounded p-3 dark:border-slate-700">
              <div class="flex flex-wrap items-center gap-2">
                <Link
                  :href="route('purchases.quotations.show', quotation.id)"
                  class="px-2 py-1 rounded bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200"
                >
                  {{ quotation.numero }} ({{ quotation.status }})
                </Link>
              </div>
            </div>
          </div>
        </div>
        <div v-if="!props.requisition.orders?.length && !props.requisition.quotations?.length" class="text-sm text-slate-500 dark:text-slate-400">Nenhum pedido vinculado.</div>
      </div>
    </div>
  </div>
</template>
