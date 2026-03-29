<script setup>
import { Head } from '@inertiajs/vue3'
import CalendarioVendas from '@/Components/CalendarioVendas.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  year: { type: Number, default: null },
  month: { type: Number, default: null },
  fetchEndpoint: { type: String, default: '' },
  salesByDate: { type: Object, default: () => ({}) }
})
</script>

<template>
  <Head :title="$t('Sales Calendar')" />
  <div class="p-4 sm:p-6">
    <h1 class="text-2xl font-bold mb-4">{{ $t('Sales Calendar') }}</h1>

    <CalendarioVendas
      :sales-by-date="salesByDate"
      :initial-year="year"
      :initial-month="month"
      :fetch-endpoint="fetchEndpoint"
      :locale="$i18n.locale === 'pt' ? 'pt-BR' : ($i18n.locale === 'es' ? 'es-ES' : 'en-US')"
    />
  </div>
</template>
