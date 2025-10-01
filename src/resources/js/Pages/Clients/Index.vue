<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'
import EditButton from '@/Components/EditButton.vue'
import ButtonStatus from '@/Components/ButtonStatus.vue'

const props = defineProps({
  filters: Object,
  clientes: Object,   // paginator
  segmentos: Array,
  ufs: Array
})

const form = reactive({
  q: props.filters.q ?? '',
  uf: props.filters.uf ?? '',
  segment_id: props.filters.segment_id ?? '',
  status: props.filters.status ?? ''
})

watch(form, () => {
  router.get(route('clientes.index'), form, { preserveState: true, replace: true })
})
</script>

<template>

  <Head title="Clientes" />

  <div class="bg-white p-4 rounded-md w-full">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-slate-700">Clientes</h2>
      <div class="flex gap-4">
        <Link :href="route('dashboard.index')"
          class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-angle-left mr-2"></i>Voltar
        </Link>
        <Link :href="route('clientes.create')"
          class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Novo Cliente
        </Link>
      </div>
    </div>

    <!-- Busca (linha principal igual ao Fornecedor) -->
    <div class="mb-6">
      <div class="flex gap-2 w-full md:w-1/2">
        <input v-model="form.q" type="text"
          class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-cyan-500"
          placeholder="Buscar (nome, doc, whatsapp, email)" />
        <button type="button" @click="router.get(route('clientes.index'), form, { preserveState: true, replace: true })"
          class="px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-full transition-colors">
          Pesquisar
        </button>
      </div>

      <!-- Filtros extras (mantidos, mas discretos) -->
      <div class="mt-3 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-2">
        <select v-model="form.uf"
          class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
          <option value="">UF</option>
          <option v-for="u in ufs" :key="u" :value="u">{{ u }}</option>
        </select>
        <select v-model="form.segment_id"
          class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
          <option value="">Segmento</option>
          <option v-for="s in segmentos" :key="s.id" :value="s.id">{{ s.nome }}</option>
        </select>
        <select v-model="form.status"
          class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
          <option value="">Status</option>
          <option :value="1">Ativo</option>
          <option :value="0">Inativo</option>
        </select>
      </div>
    </div>

    <!-- Tabela (estrutura/classe espelhando Fornecedores) -->
    <div class="overflow-x-auto rounded-lg border">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nome</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden md:table-cell">Documento</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden lg:table-cell">WhatsApp</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">UF</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden xl:table-cell">Segmento</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="c in clientes.data" :key="c.id_cliente" class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm text-gray-700">
              <Link :href="route('clientes.show', c.id_cliente)" class="text-blue-600 hover:underline">
              {{ c.nome_fantasia || c.razao_social || c.nome || '—' }}
              </Link>
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">
              {{ c.documento || '—' }}
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 hidden lg:table-cell">
              {{ c.whatsapp || '—' }}
            </td>
            <td class="px-4 py-3 text-sm text-gray-700">
              {{ c.uf || '—' }}
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 hidden xl:table-cell">
              {{ c.segmento?.nome || '—' }}
            </td>
            <td class="px-4 py-3 text-sm">
              <span :class="c.status ? 'text-green-700' : 'text-gray-500'">
                {{ c.status ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="px-4 py-3 text-sm">
              <div class="flex gap-2">
                <EditButton :route-name="'clientes.edit'" :model-id="c.id_cliente"
                  :can-edit="$page.props.auth?.can?.edit_client ?? true" />

                <ButtonStatus model-name="cliente" :model-id="c.id_cliente" :status="c.status"
                  :can-toggle="$page.props.auth?.can?.toggle_status ?? true"
                  @toggled="(newStatus) => c.status = newStatus" />
              </div>
            </td>
          </tr>

          <tr v-if="clientes.data.length === 0">
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
              Nenhum cliente encontrado.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginação (visual semelhante ao padrão do Laravel) -->
    <div class="mt-4 flex flex-wrap gap-2">
      <Link v-for="link in clientes.links" :key="(link.url || '') + link.label" :href="link.url || '#'"
        v-html="link.label" preserve-state class="px-3 py-1 rounded border text-sm" :class="{
          'bg-blue-600 text-white border-blue-600': link.active,
          'text-gray-600 hover:bg-gray-50': !link.active,
          'pointer-events-none opacity-50': !link.url
        }" />
    </div>
  </div>
</template>
