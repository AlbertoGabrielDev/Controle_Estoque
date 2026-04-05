<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import OrderForm from './OrderForm.vue'

const props = defineProps({
  order: { type: Object, required: true },
  clientes_options: { type: Array, default: () => [] },
  items_options: { type: Array, default: () => [] },
  unidades_options: { type: Array, default: () => [] },
  impostos_options: { type: Array, default: () => [] },
})

const form = useForm({
  cliente_id: props.order.cliente_id ?? '',
  data_pedido: props.order.data_pedido ?? '',
  observacoes: props.order.observacoes ?? '',
  total: props.order.total ?? 0,
  items: (props.order.items ?? []).map(i => ({
    _item_obj: null, item_id: i.item_id, descricao_snapshot: i.descricao_snapshot,
    unidade_medida_id: i.unidade_medida_id ?? '', _unidade_obj: null, _imposto_obj: null,
    imposto_id: i.imposto_id ?? '', quantidade: i.quantidade, preco_unit: i.preco_unit,
    desconto_percent: i.desconto_percent ?? 0, desconto_valor: i.desconto_valor ?? 0,
    aliquota_snapshot: i.aliquota_snapshot ?? 0, total_linha: i.total_linha,
  })),
})

function submit() { form.patch(route('commercial.orders.update', props.order.id)) }
</script>

<template>
  <Head :title="$t('Edit Order')" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-700 dark:text-slate-100">{{ $t('Edit Order') }} {{ order.numero }}</h1>
    <Link :href="route('commercial.orders.show', order.id)" class="text-blue-600 dark:text-blue-400">{{ $t('Back') }}</Link>
  </div>
  <OrderForm :form="form" :clientes-options="props.clientes_options" :items-options="props.items_options" :unidades-options="props.unidades_options" :impostos-options="props.impostos_options" :submit-label="$t('Save')" @submit="submit" />
</template>
