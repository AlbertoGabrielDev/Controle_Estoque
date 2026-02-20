<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import ButtonStatus from '@/components/ButtonStatus.vue'

const props = defineProps({
  categoriaId: {
    type: Number,
    required: true,
  },
  categoria: {
    type: String,
    default: '',
  },
  produtos: {
    type: Object,
    default: () => ({}),
  },
})

const rows = computed(() => props.produtos?.data ?? [])
const currentPage = computed(() => Number(props.produtos?.current_page ?? 1))
const lastPage = computed(() => Number(props.produtos?.last_page ?? 1))

function goToPage(page) {
  if (!page || page < 1 || page > lastPage.value) {
    return
  }

  router.get(
    route('categorias.produto', props.categoriaId),
    { page },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      only: ['produtos'],
    }
  )
}

function formatNutrition(value) {
  if (value === null || value === undefined || value === '') {
    return '-'
  }

  if (typeof value === 'string') {
    return value
  }

  try {
    return JSON.stringify(value)
  } catch (_) {
    return String(value)
  }
}
</script>

<template>
  <Head :title="`Produtos da categoria ${categoria || ''}`" />

  <div class="bg-white p-4 rounded-md w-full">
    <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      <h1 class="text-2xl font-semibold text-slate-700">
        Produtos da Categoria: {{ categoria || `#${categoriaId}` }}
      </h1>
      <div class="flex gap-2">
        <Link
          :href="route('categoria.inicio')"
          class="flex items-center rounded-md bg-gray-100 px-4 py-2 text-gray-800 transition-colors hover:bg-gray-200"
        >
          <i class="fas fa-angle-left mr-2"></i>
          Voltar
        </Link>
        <Link
          :href="route('produtos.cadastro')"
          class="flex items-center rounded-md bg-gray-100 px-4 py-2 text-gray-800 transition-colors hover:bg-gray-200"
        >
          <i class="fas fa-plus mr-2"></i>
          Cadastrar Produto
        </Link>
      </div>
    </div>

    <div class="overflow-x-auto border rounded-lg">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Cod. Produto</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Nome</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Descricao</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Unidade</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Info. Nutricional</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Editar</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="produto in rows"
            :key="produto.id_produto"
            class="border-t border-gray-200"
          >
            <td class="px-4 py-3">{{ produto.cod_produto }}</td>
            <td class="px-4 py-3">{{ produto.nome_produto }}</td>
            <td class="px-4 py-3">{{ produto.descricao }}</td>
            <td class="px-4 py-3">{{ produto.unidade_medida }}</td>
            <td class="px-4 py-3 max-w-xs truncate" :title="formatNutrition(produto.inf_nutriente)">
              {{ formatNutrition(produto.inf_nutriente) }}
            </td>
            <td class="px-4 py-3">
              <Link
                :href="route('produtos.editar', produto.id_produto)"
                class="inline-flex items-center justify-center rounded-md p-2 text-cyan-600 transition hover:bg-cyan-50"
                title="Editar"
              >
                <i class="fas fa-edit"></i>
              </Link>
            </td>
            <td class="px-4 py-3">
              <ButtonStatus
                :model-id="produto.id_produto"
                :status="produto.status"
                model-name="produto"
                toggle-route-name="produto.status"
              />
            </td>
          </tr>

          <tr v-if="rows.length === 0">
            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
              Nenhum produto encontrado para esta categoria.
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
