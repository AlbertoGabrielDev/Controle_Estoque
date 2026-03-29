<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: 'Salvar' },
  ordersOptions: { type: Array, default: () => [] },
})

const selectedOrder = ref(null)

onMounted(() => {
  if (props.form.order_id && props.ordersOptions) {
    selectedOrder.value = props.ordersOptions.find(o => o.id === props.form.order_id) || null
  }
})

const isOrderIncomplete = computed(() => {
  return selectedOrder.value?.status === 'parcialmente_recebido'
})

watch(selectedOrder, (newVal) => {
  props.form.order_id = newVal ? newVal.id : ''

  // Auto-preencher itens do pedido selecionado
  if (newVal && newVal.items && newVal.items.length) {
    props.form.items = newVal.items.map(orderItem => {
      const jaRecebido = Number(orderItem.quantidade_recebida || 0)
      const pedido = Number(orderItem.quantidade_pedida)
      const restante = pedido - jaRecebido

      return {
        order_item_id: orderItem.id,
        descricao: orderItem.descricao_snapshot || '',
        quantidade_pedida: pedido,
        quantidade_ja_recebida: jaRecebido,
        quantidade_restante: restante,
        quantidade_recebida: restante > 0 ? restante : 0,
        preco_unit_recebido: orderItem.preco_unit || 0,
        imposto_id: '',
        aliquota_snapshot: '',
      }
    })
  } else {
    props.form.items = []
  }
})

function customOrderLabel(option) {
  return option ? `${option.numero} (${option.status === 'parcialmente_recebido' ? 'Parcial' : 'Emitido'})` : ''
}

const totalQuantidade = computed(() => props.form.items.reduce((total, item) => {
  const value = Number(item.quantidade_recebida)
  return total + (Number.isNaN(value) ? 0 : value)
}, 0))

const totalValor = computed(() => props.form.items.reduce((total, item) => {
  const quantidade = Number(item.quantidade_recebida)
  const preco = Number(item.preco_unit_recebido)
  const qty = Number.isNaN(quantidade) ? 0 : quantidade
  const unit = Number.isNaN(preco) ? 0 : preco
  return total + (qty * unit)
}, 0))
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow dark:bg-slate-900 dark:border dark:border-slate-700 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Pedido') }}</label>
        <Multiselect
          v-model="selectedOrder"
          :options="props.ordersOptions"
          :custom-label="customOrderLabel"
          track-by="id"
          :placeholder="$t('Buscar Pedido')"
          select-label="Enter para selecionar"
          deselect-label=""
          :use-teleport="true"
          class="w-full text-sm mt-1"
        />
        <div v-if="props.form.errors.order_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.order_id }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">Data Recebimento <span class="text-red-500">*</span></label>
        <input v-model="props.form.data_recebimento" type="date" required class="mt-1 border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500 outline-none">
        <div v-if="props.form.errors.data_recebimento" class="text-red-600 text-sm mt-1">{{ props.form.errors.data_recebimento }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Observacoes') }}</label>
        <input v-model="props.form.observacoes" class="mt-1 border rounded px-3 py-2 w-full">
      </div>
    </div>

    <!-- Alerta de Pedido Parcial -->
    <div v-if="isOrderIncomplete" class="bg-amber-50 border-l-4 border-amber-400 p-4 dark:bg-amber-900/20 dark:border-amber-600">
      <div class="flex">
        <div class="flex-shrink-0">
          <i class="fas fa-exclamation-triangle text-amber-400"></i>
        </div>
        <div class="ml-3">
          <p class="text-sm text-amber-700 dark:text-amber-300">
            <strong>Atenção:</strong> Este pedido possui recebimentos anteriores. 
            Abaixo são mostradas as quantidades <strong>restantes</strong> para completar o pedido.
          </p>
        </div>
      </div>
    </div>

    <!-- Itens do Pedido (preenchidos automaticamente) -->
    <div v-if="selectedOrder" class="space-y-2">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-slate-800 dark:text-slate-200">
          Itens do Pedido: <span class="text-blue-600">{{ selectedOrder.numero }}</span>
        </h3>
        <span v-if="isOrderIncomplete" class="px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
          Incompleto
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm border purchases-table dark:border-slate-700">
          <thead class="bg-slate-50 dark:bg-slate-800/70">
            <tr>
              <th class="px-3 py-2 text-left">Descricao</th>
              <th class="px-3 py-2 text-center">Qtd Pedida</th>
              <th class="px-3 py-2 text-center">Ja Recebida</th>
              <th class="px-3 py-2 text-center bg-blue-50/50 dark:bg-blue-900/20">Saldo Restante</th>
              <th class="px-3 py-2 text-left">Qtd a Receber Hoje</th>
              <th class="px-3 py-2 text-left">Preco Unit</th>
              <th class="px-3 py-2 text-left">Imposto ID</th>
              <th class="px-3 py-2 text-left">Aliquota</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in props.form.items" :key="index" class="border-t dark:border-slate-700 hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
              <td class="px-3 py-2 text-slate-700 dark:text-slate-300 font-medium">
                {{ item.descricao }}
                <div v-if="item.quantidade_restante <= 0" class="text-[10px] uppercase font-bold text-green-600 dark:text-green-500 mt-0.5">
                  <i class="fas fa-check-circle mr-1"></i>Concluído
                </div>
              </td>
              <td class="px-3 py-2 text-center text-slate-500 dark:text-slate-400">{{ item.quantidade_pedida }}</td>
              <td class="px-3 py-2 text-center">
                <span :class="item.quantidade_ja_recebida > 0 ? (item.quantidade_restante <= 0 ? 'text-green-600' : 'text-blue-600 font-semibold') : 'text-slate-500'">
                  {{ item.quantidade_ja_recebida }}
                </span>
              </td>
              <td class="px-3 py-2 text-center bg-blue-50/30 dark:bg-blue-900/10 font-bold" :class="item.quantidade_restante <= 0 ? 'text-slate-400 dark:text-slate-600' : 'text-blue-700 dark:text-blue-300'">
                {{ item.quantidade_restante }}
              </td>
              <td class="px-3 py-2 min-w-[120px]">
                <div v-if="item.quantidade_restante <= 0" class="text-xs text-slate-400 py-2 italic text-center">
                  Item já recebido
                </div>
                <template v-else>
                  <input 
                    v-model="item.quantidade_recebida" 
                    type="number" 
                    step="0.001" 
                    min="0" 
                    :max="item.quantidade_restante" 
                    class="border rounded px-2 py-1 w-full focus:ring-2 focus:ring-blue-500 outline-none"
                    :class="item.quantidade_recebida > 0 ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800' : ''"
                  >
                  <div v-if="props.form.errors[`items.${index}.quantidade_recebida`]" class="text-red-600 text-xs mt-1">
                    {{ props.form.errors[`items.${index}.quantidade_recebida`] }}
                  </div>
                </template>
              </td>
              <td class="px-3 py-2">
                <input v-model="item.preco_unit_recebido" type="number" step="0.01" min="0" class="border rounded px-2 py-1 w-full focus:ring-2 focus:ring-blue-500 outline-none">
              </td>
              <td class="px-3 py-2">
                <input v-model="item.imposto_id" type="number" min="1" class="border rounded px-2 py-1 w-full focus:ring-2 focus:ring-blue-500 outline-none">
              </td>
              <td class="px-3 py-2">
                <input v-model="item.aliquota_snapshot" type="number" step="0.01" min="0" class="border rounded px-2 py-1 w-full focus:ring-2 focus:ring-blue-500 outline-none">
              </td>
            </tr>
            <tr v-if="!props.form.items.length">
              <td colspan="8" class="px-3 py-3 text-center text-slate-500 dark:text-slate-400 italic">Nenhum item pendente no pedido.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else class="bg-slate-50 dark:bg-slate-800/50 rounded-lg border-2 border-dashed border-slate-200 dark:border-slate-700 p-8 text-center">
      <div class="mb-2">
        <i class="fas fa-file-invoice text-slate-300 dark:text-slate-600 text-3xl"></i>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Selecione um Pedido acima para visualizar e registrar os itens a receber.
      </p>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 text-sm font-medium border-t dark:border-slate-700 pt-4 px-1">
      <div class="flex gap-4">
         <div class="text-slate-500">Total de itens: <span class="text-slate-800 dark:text-slate-200">{{ props.form.items.length }}</span></div>
         <div class="text-slate-500">Quantidade total: <span class="text-slate-800 dark:text-slate-200">{{ totalQuantidade.toFixed(3) }}</span></div>
      </div>
      <div class="text-lg text-blue-700 dark:text-blue-400">
        Valor do Recebimento: <span class="font-bold">R$ {{ totalValor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
      </div>
    </div>

    <div class="flex justify-end pt-2">
      <button 
        :disabled="props.form.processing || !selectedOrder" 
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <i v-if="props.form.processing" class="fas fa-spinner fa-spin mr-2"></i>
        <span>{{ submitLabel }}</span>
      </button>
    </div>
  </form>
</template>
