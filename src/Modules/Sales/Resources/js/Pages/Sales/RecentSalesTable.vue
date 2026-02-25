<script setup>
import { computed } from 'vue'

const props = defineProps({
  sales: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['page-change'])

const rows = computed(() => props.sales?.data ?? [])
const currentPage = computed(() => Number(props.sales?.current_page ?? 1))
const lastPage = computed(() => Number(props.sales?.last_page ?? 1))

function money(value) {
  return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function goPrev() {
  if (currentPage.value <= 1) {
    return
  }

  emit('page-change', currentPage.value - 1)
}

function goNext() {
  if (currentPage.value >= lastPage.value) {
    return
  }

  emit('page-change', currentPage.value + 1)
}
</script>

<template>
  <section class="mt-8">
    <h2 class="text-xl md:text-2xl font-bold mb-4 text-center md:text-left">
      Ultimos Produtos Vendidos
    </h2>

    <div class="overflow-x-auto border rounded-lg bg-white">
      <table class="table-auto w-full border-collapse">
        <thead class="bg-gray-100">
          <tr class="text-sm md:text-base text-gray-600">
            <th class="py-3 px-4 md:px-6 text-left font-medium">Nome</th>
            <th class="py-3 px-4 md:px-6 text-left font-medium">Preco Venda</th>
            <th class="py-3 px-4 md:px-6 text-left font-medium">Cod. Produto</th>
            <th class="py-3 px-4 md:px-6 text-left font-medium">Quantidade</th>
            <th class="py-3 px-4 md:px-6 text-left font-medium">Vendedor</th>
            <th class="py-3 px-4 md:px-6 text-left font-medium">Data</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="sale in rows"
            :key="sale.id"
            class="border-b border-gray-200 hover:bg-gray-50 text-sm md:text-base"
          >
            <td class="py-3 px-4 md:px-6 text-gray-700">{{ sale.nome_produto }}</td>
            <td class="py-3 px-4 md:px-6 text-gray-700">{{ money(sale.preco_venda) }}</td>
            <td class="py-3 px-4 md:px-6 text-gray-700">{{ sale.cod_produto }}</td>
            <td class="py-3 px-4 md:px-6 text-gray-700">{{ sale.quantidade }}</td>
            <td class="py-3 px-4 md:px-6 text-gray-700">{{ sale.vendedor || '-' }}</td>
            <td class="py-3 px-4 md:px-6 text-gray-700">{{ sale.data || '-' }}</td>
          </tr>

          <tr v-if="rows.length === 0">
            <td colspan="6" class="py-8 px-4 text-center text-gray-500">
              Nenhuma venda registrada ate o momento.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-center items-center gap-3">
      <button
        type="button"
        class="py-2 px-3 bg-gray-100 border rounded-lg hover:bg-gray-200 text-gray-600 disabled:opacity-50"
        :disabled="currentPage <= 1"
        @click="goPrev"
      >
        Anterior
      </button>
      <span class="py-2 px-3 bg-gray-100 text-gray-600 rounded-lg">
        Pagina {{ currentPage }} de {{ lastPage }}
      </span>
      <button
        type="button"
        class="py-2 px-3 bg-gray-100 border rounded-lg hover:bg-gray-200 text-gray-600 disabled:opacity-50"
        :disabled="currentPage >= lastPage"
        @click="goNext"
      >
        Proxima
      </button>
    </div>
  </section>
</template>
