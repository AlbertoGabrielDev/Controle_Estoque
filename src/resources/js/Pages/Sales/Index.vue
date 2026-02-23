<script setup>
import { computed, ref } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
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
  requireClient: {
    type: Boolean,
    default: true,
  },
})

const page = usePage()
const userId = page.props.auth?.user?.id ?? null

const {
  client,
  setClient,
  cartItems,
  cartTotal,
  busy,
  finalizing,
  addByManualCode,
  addByQrCode,
  addSelectedProduct,
  loadCartMerge,
  changeQuantity,
  removeItem,
  finalizeSale,
} = useCart({ requireClient: props.requireClient, userId })

const manualOptions = ref([])

const clientModel = computed({
  get: () => client.value,
  set: (value) => setClient(value),
})

async function onManualSubmit(code) {
  manualOptions.value = []
  const result = await addByManualCode(code)
  if (result?.opcoes?.length) {
    manualOptions.value = result.opcoes
  }
}

async function onManualSelect(option) {
  manualOptions.value = []
  await addSelectedProduct(option, 1)
}

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
      :required="props.requireClient"
      :loading="busy"
      @load-cart="loadCartMerge"
    />

    <ManualCodeInput
      :disabled="busy || finalizing"
      :options="manualOptions"
      @submit="onManualSubmit"
      @select="onManualSelect"
      @clear-options="manualOptions = []"
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
