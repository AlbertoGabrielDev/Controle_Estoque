<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { reactive } from 'vue'

const props = defineProps({
  quotation: { type: Object, required: true },
})

const addSupplierForm = useForm({
  supplier_id: '',
})

const supplierForms = reactive({})

for (const supplier of props.quotation.suppliers ?? []) {
  supplierForms[supplier.id] = useForm({
    items: (supplier.items ?? []).map((item) => ({
      requisition_item_id: item.requisition_item_id,
      quantidade: item.quantidade ?? 0,
      preco_unit: item.preco_unit ?? 0,
      imposto_id: item.imposto_id ?? '',
      aliquota_snapshot: item.aliquota_snapshot ?? '',
    })),
  })
}

/**
 * Submit the add supplier request.
 *
 * @returns {void}
 */
function submitAddSupplier() {
  addSupplierForm.post(route('purchases.quotations.addSupplier', props.quotation.id))
}

/**
 * Submit prices for a supplier.
 *
 * @param {number} supplierId
 * @returns {void}
 */
function submitPrices(supplierId) {
  const form = supplierForms[supplierId]
  if (!form) return
  form.patch(route('purchases.quotations.registerPrices', {
    quotationId: props.quotation.id,
    quotationSupplierId: supplierId,
  }))
}
</script>

<template>
  <Head title="Cotacao" />

  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
      <h2 class="text-2xl font-semibold">Cotacao {{ props.quotation.numero }}</h2>
      <div class="text-sm text-slate-600">Status: {{ props.quotation.status }}</div>
    </div>
    <div class="flex flex-wrap gap-2">
      <Link :href="route('purchases.quotations.index')" class="text-blue-600">Voltar</Link>
      <Link v-if="props.quotation.status === 'aberta'" :href="route('purchases.quotations.edit', props.quotation.id)" class="text-blue-600">Editar</Link>
      <Link
        v-if="props.quotation.status === 'aberta'"
        method="patch"
        as="button"
        :href="route('purchases.quotations.close', props.quotation.id)"
        class="px-3 py-1 rounded bg-green-600 text-white"
      >
        Encerrar
      </Link>
      <Link
        v-if="props.quotation.status !== 'cancelada'"
        method="patch"
        as="button"
        :href="route('purchases.quotations.cancel', props.quotation.id)"
        class="px-3 py-1 rounded bg-red-600 text-white"
      >
        Cancelar
      </Link>
      <Link
        v-if="props.quotation.status === 'encerrada'"
        method="post"
        as="button"
        :href="route('purchases.orders.fromQuotation')"
        :data="{ quotation_id: props.quotation.id }"
        class="px-3 py-1 rounded bg-slate-700 text-white"
      >
        Gerar Pedidos
      </Link>
    </div>
  </div>

  <div class="bg-white rounded shadow p-4 space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <div class="text-xs text-slate-500">Requisicao</div>
        <Link :href="route('purchases.requisitions.show', props.quotation.requisition_id)" class="text-blue-600">
          {{ props.quotation.requisition_id }}
        </Link>
      </div>
      <div>
        <div class="text-xs text-slate-500">Data limite</div>
        <div class="font-medium">{{ props.quotation.data_limite ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-slate-500">Observacoes</div>
        <div class="font-medium">{{ props.quotation.observacoes ?? '-' }}</div>
      </div>
    </div>

    <div class="border-t pt-4">
      <h3 class="font-semibold mb-3">Adicionar fornecedor</h3>
      <form @submit.prevent="submitAddSupplier" class="flex flex-wrap gap-2 items-center">
        <input v-model="addSupplierForm.supplier_id" type="number" min="1" placeholder="Fornecedor ID" class="border rounded px-3 py-2">
        <button :disabled="addSupplierForm.processing" class="px-3 py-2 rounded bg-gray-100 hover:bg-gray-200">
          Adicionar
        </button>
      </form>
    </div>

    <div class="border-t pt-4 space-y-4">
      <h3 class="font-semibold">Fornecedores</h3>

      <div v-if="!props.quotation.suppliers?.length" class="text-sm text-slate-500">
        Nenhum fornecedor cadastrado.
      </div>

      <div v-for="supplier in props.quotation.suppliers" :key="supplier.id" class="border rounded p-4 space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <div>
            <div class="text-sm text-slate-500">Fornecedor</div>
            <div class="font-medium">
              {{ supplier.supplier?.nome_fornecedor ?? supplier.supplier?.razao_social ?? supplier.supplier_id }}
            </div>
          </div>
          <div class="text-sm text-slate-600">Status: {{ supplier.status }}</div>
        </div>

        <form v-if="supplierForms[supplier.id]" @submit.prevent="submitPrices(supplier.id)">
          <div class="overflow-x-auto">
            <table class="w-full text-sm border">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-3 py-2 text-left">Req Item</th>
                  <th class="px-3 py-2 text-left">Quantidade</th>
                  <th class="px-3 py-2 text-left">Preco Unit</th>
                  <th class="px-3 py-2 text-left">Imposto ID</th>
                  <th class="px-3 py-2 text-left">Aliquota</th>
                  <th class="px-3 py-2 text-left">Vencedor</th>
                  <th class="px-3 py-2 text-left">Acoes</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in supplier.items"
                  :key="item.id"
                  class="border-t"
                >
                  <td class="px-3 py-2">{{ item.requisition_item_id }}</td>
                  <td class="px-3 py-2">
                    <input v-model="supplierForms[supplier.id].items[index].quantidade" type="number" step="0.001" min="0" class="border rounded px-2 py-1 w-full">
                  </td>
                  <td class="px-3 py-2">
                    <input v-model="supplierForms[supplier.id].items[index].preco_unit" type="number" step="0.01" min="0" class="border rounded px-2 py-1 w-full">
                  </td>
                  <td class="px-3 py-2">
                    <input v-model="supplierForms[supplier.id].items[index].imposto_id" type="number" min="1" class="border rounded px-2 py-1 w-full">
                  </td>
                  <td class="px-3 py-2">
                    <input v-model="supplierForms[supplier.id].items[index].aliquota_snapshot" type="number" step="0.01" min="0" class="border rounded px-2 py-1 w-full">
                  </td>
                  <td class="px-3 py-2">{{ item.selecionado ? 'Sim' : 'Nao' }}</td>
                  <td class="px-3 py-2">
                    <Link
                      v-if="props.quotation.status === 'aberta'"
                      method="patch"
                      as="button"
                      :href="route('purchases.quotations.selectItem', props.quotation.id)"
                      :data="{ quotation_supplier_item_id: item.id }"
                      class="px-2 py-1 rounded bg-blue-600 text-white"
                    >
                      Selecionar
                    </Link>
                  </td>
                </tr>
                <tr v-if="!supplier.items?.length">
                  <td colspan="7" class="px-3 py-3 text-center text-slate-500">Nenhum item.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-3 flex justify-end">
            <button :disabled="supplierForms[supplier.id].processing" class="px-3 py-2 rounded bg-slate-700 text-white">
              Salvar precos
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="border-t pt-4">
      <h3 class="font-semibold mb-2">Pedidos Gerados</h3>
      <div v-if="props.quotation.orders?.length" class="flex flex-wrap gap-2">
        <Link
          v-for="order in props.quotation.orders"
          :key="order.id"
          :href="route('purchases.orders.show', order.id)"
          class="px-2 py-1 rounded bg-slate-100 text-slate-700"
        >
          {{ order.numero }} ({{ order.status }})
        </Link>
      </div>
      <div v-else class="text-sm text-slate-500">Nenhum pedido gerado.</div>
    </div>
  </div>
</template>
