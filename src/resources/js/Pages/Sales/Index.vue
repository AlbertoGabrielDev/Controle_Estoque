<script setup>
import { computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import CartTable from './CartTable.vue'
import ClientSelector from './ClientSelector.vue'
import ManualCodeInput from './ManualCodeInput.vue'
import QrScanner from './QrScanner.vue'
import RecentSalesTable from './RecentSalesTable.vue'
import { useCart } from '@/composables/useCart'

const props = defineProps({
  vendas: {
    type: Object,
    default: () => ({}),
  },
})

const {
  client,
  setClient,
  cartItems,
  cartTotal,
  busy,
  finalizing,
  addByManualCode,
  addByQrCode,
  loadCartMerge,
  changeQuantity,
  removeItem,
  finalizeSale,
} = useCart()

const clientModel = computed({
  get: () => client.value,
  set: (value) => setClient(value),
})

async function onFinalizeSale() {
  const success = await finalizeSale()
  if (!success) {
    return
  }

  router.reload({
    only: ['vendas'],
    preserveState: true,
    preserveScroll: true,
  })
}

function goToSalesPage(page) {
  if (!page || page < 1) {
    return
  }

  router.get(
    route('vendas.venda'),
    { page },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      only: ['vendas'],
    }
  )
}
</script>

<template>
  <Head title="Registrar Venda" />

  <div class="bg-white p-4 md:p-6 rounded-md shadow-md w-full max-w-7xl mx-auto">
    <header class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-center md:text-left">
        Registrar Venda
      </h1>
    </header>

    <ClientSelector
      v-model="clientModel"
      :loading="busy"
      @load-cart="loadCartMerge"
    />

    <ManualCodeInput
      :disabled="busy || finalizing"
      @submit="addByManualCode"
    />

    <QrScanner
      :disabled="busy || finalizing"
      @decoded="addByQrCode"
    />

    <CartTable
      :items="cartItems"
      :total="cartTotal"
      :loading="busy"
      :finalizing="finalizing"
      @increment="(item) => changeQuantity(item, 1)"
      @decrement="(item) => changeQuantity(item, -1)"
      @remove="removeItem"
      @finalize="onFinalizeSale"
    />

    <RecentSalesTable
      :sales="vendas"
      @page-change="goToSalesPage"
    />
  </div>
</template>
