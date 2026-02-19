<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps({
  historicos: {
    type: Object,
    default: () => ({}),
  },
})

const rows = computed(() => props.historicos?.data ?? [])
const currentPage = computed(() => Number(props.historicos?.current_page ?? 1))
const lastPage = computed(() => Number(props.historicos?.last_page ?? 1))

function money(value) {
  return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function goToPage(page) {
  if (!page || page < 1 || page > lastPage.value) {
    return
  }

  router.get(
    route('estoque.historico'),
    { page },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      only: ['historicos'],
    }
  )
}
</script>

<template>
  <Head title="Historico de Estoque" />

  <div class="bg-white p-4 rounded-md w-full">
    <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      <h1 class="text-2xl font-semibold text-slate-700">Historico de Estoque</h1>
      <Link
        :href="route('estoque.index')"
        class="rounded bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200 w-fit"
      >
        Voltar
      </Link>
    </div>

    <div class="overflow-x-auto border rounded-lg">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Produto</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Marca</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Fornecedor</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Qtde. Retirada</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Qtde. Estoque</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Venda</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Data de Alteracao</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in rows"
            :key="item.id"
            class="border-t border-gray-200"
          >
            <td class="px-4 py-3">{{ item.produto || '-' }}</td>
            <td class="px-4 py-3">{{ item.marca || '-' }}</td>
            <td class="px-4 py-3">{{ item.fornecedor || '-' }}</td>
            <td class="px-4 py-3">{{ item.quantidade_retirada }}</td>
            <td class="px-4 py-3">{{ item.quantidade }}</td>
            <td class="px-4 py-3">{{ money(item.venda) }}</td>
            <td class="px-4 py-3">{{ item.data_alteracao || '-' }}</td>
          </tr>

          <tr v-if="rows.length === 0">
            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
              Nenhum historico encontrado.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex items-center justify-center gap-3">
      <button
        type="button"
        class="rounded border bg-gray-100 px-3 py-2 text-gray-700 hover:bg-gray-200 disabled:opacity-50"
        :disabled="currentPage <= 1"
        @click="goToPage(currentPage - 1)"
      >
        Anterior
      </button>
      <span class="rounded bg-gray-100 px-3 py-2 text-sm text-gray-600">
        Pagina {{ currentPage }} de {{ lastPage }}
      </span>
      <button
        type="button"
        class="rounded border bg-gray-100 px-3 py-2 text-gray-700 hover:bg-gray-200 disabled:opacity-50"
        :disabled="currentPage >= lastPage"
        @click="goToPage(currentPage + 1)"
      >
        Proxima
      </button>
    </div>
  </div>
</template>
