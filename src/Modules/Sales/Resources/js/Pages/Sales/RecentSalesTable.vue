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
  <section class="mt-8 recent-sales-table">
    <h2 class="text-xl md:text-2xl font-bold mb-4 text-center md:text-left text-slate-900 dark:text-slate-100">
      Ultimos Produtos Vendidos
    </h2>

    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/80">
      <table class="table-auto w-full border-collapse">
        <thead class="bg-slate-50 dark:bg-slate-800/70">
          <tr class="text-sm md:text-base text-slate-600 dark:text-slate-300">
            <th class="py-3 px-4 md:px-6 text-left font-medium">Nome</th>
            <th class="py-3 px-4 md:px-6 text-left font-medium">Preço Venda</th>
            <th class="py-3 px-4 md:px-6 text-left font-medium">Preço Total</th>
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
            class="border-b border-slate-200 dark:border-slate-700 text-sm md:text-base"
          >
            <td class="py-3 px-4 md:px-6 text-slate-700 dark:text-slate-200">{{ sale.nome_produto }}</td>
            <td class="py-3 px-4 md:px-6 text-slate-700 dark:text-slate-200">
              <span v-if="sale.preco_unit != null">{{ money(sale.preco_unit) }}</span>
              <span v-else>-</span>
            </td>
            <td class="py-3 px-4 md:px-6 text-slate-700 dark:text-slate-200">
              <span v-if="sale.preco_total != null">{{ money(sale.preco_total) }}</span>
              <span v-else>-</span>
            </td>
            <td class="py-3 px-4 md:px-6 text-slate-700 dark:text-slate-200">{{ sale.cod_produto }}</td>
            <td class="py-3 px-4 md:px-6 text-slate-700 dark:text-slate-200">{{ sale.quantidade }}</td>
            <td class="py-3 px-4 md:px-6 text-slate-700 dark:text-slate-200">{{ sale.vendedor || '-' }}</td>
            <td class="py-3 px-4 md:px-6 text-slate-700 dark:text-slate-200">{{ sale.data || '-' }}</td>
          </tr>

          <tr v-if="rows.length === 0">
            <td colspan="7" class="py-8 px-4 text-center text-slate-500 dark:text-slate-400">
              Nenhuma venda registrada ate o momento.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-center items-center gap-3">
      <button
        type="button"
        class="py-2 px-3 rounded-lg border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
        :disabled="currentPage <= 1"
        @click="goPrev"
      >
        Anterior
      </button>
      <span class="py-2 px-3 rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-200">
        Pagina {{ currentPage }} de {{ lastPage }}
      </span>
      <button
        type="button"
        class="py-2 px-3 rounded-lg border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
        :disabled="currentPage >= lastPage"
        @click="goNext"
      >
        Proxima
      </button>
    </div>
  </section>
</template>

<style scoped>
.recent-sales-table table thead th {
  background-color: #f8fafc;
}

.dark .recent-sales-table table thead th {
  background-color: #0f172a;
}

.recent-sales-table table tbody tr:hover td {
  background-color: transparent;
}

.dark .recent-sales-table table tbody tr:hover td {
  background-color: transparent;
}
</style>



