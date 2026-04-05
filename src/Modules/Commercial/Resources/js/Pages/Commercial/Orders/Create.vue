<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import OrderForm from './OrderForm.vue'

const props = defineProps({
  clientes_options: { type: Array, default: () => [] },
  items_options: { type: Array, default: () => [] },
  unidades_options: { type: Array, default: () => [] },
  impostos_options: { type: Array, default: () => [] },
})

const form = useForm({
  cliente_id: '', data_pedido: new Date().toISOString().split('T')[0], observacoes: '', total: 0, items: [],
})

function submit() { form.post(route('commercial.orders.store')) }
</script>

<template>
  <Head :title="$t('New Sales Order')" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('New Sales Order') }}</h1>
    <Link :href="route('commercial.orders.index')" class="text-blue-600 dark:text-blue-400">{{ $t('Back') }}</Link>
  </div>
  <OrderForm :form="form" :clientes-options="props.clientes_options" :items-options="props.items_options" :unidades-options="props.unidades_options" :impostos-options="props.impostos_options" :submit-label="$t('Create Order')" @submit="submit" />
</template>
