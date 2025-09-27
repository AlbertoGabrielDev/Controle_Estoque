<script setup>
import { Head } from '@inertiajs/vue3'
import CalendarioVendas from '@/Components/CalendarioVendas.vue'

const props = defineProps({
  year: { type: Number, default: null },
  month: { type: Number, default: null },
  fetchEndpoint: { type: String, default: '' },
  salesByDate: { type: Object, default: () => ({}) }
})
</script>

<template>
  <Head title="Calendário de Vendas" />
  <div class="p-4 sm:p-6">
    <h1 class="text-2xl font-bold mb-4">Calendário de Vendas</h1>

    <CalendarioVendas
      :sales-by-date="salesByDate"
      :initial-year="year"
      :initial-month="month"
      :fetch-endpoint="fetchEndpoint"
      locale="pt-BR"
    />
  </div>
</template>
