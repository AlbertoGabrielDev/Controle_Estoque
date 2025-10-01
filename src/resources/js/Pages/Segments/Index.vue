<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  segmentos: Object,
  q: String
})
const q = ref(props.q ?? '')
watch(q, () => {
  router.get(route('segmentos.index'), { q: q.value }, { preserveState: true, replace: true })
})
</script>

<template>
  <Head title="Segmentos" />
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Segmentos</h1>
    <Link :href="route('segmentos.create')" class="px-3 py-2 rounded bg-blue-600 text-white">Novo Segmento</Link>
  </div>

  <input v-model="q" placeholder="Buscar segmento" class="border rounded px-3 py-2 mb-3 w-full md:w-1/2"/>

  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full">
      <thead class="bg-gray-50">
        <tr class="text-left">
          <th class="px-4 py-2">Nome</th>
          <th class="px-4 py-2 w-24"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="s in segmentos.data" :key="s.id" class="border-t">
          <td class="px-4 py-2">{{ s.nome }}</td>
          <td class="px-4 py-2 text-right">
            <Link :href="route('segmentos.edit', s.id)" class="px-2 py-1 text-sm rounded bg-gray-100">Editar</Link>
          </td>
        </tr>
        <tr v-if="segmentos.data.length === 0">
          <td colspan="2" class="px-4 py-6 text-center text-gray-500">Nenhum segmento encontrado.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="mt-4 flex gap-2">
    <Link
      v-for="link in segmentos.links"
      :key="link.url + link.label"
      :href="link.url || '#'"
      v-html="link.label"
      class="px-3 py-1 rounded border"
      :class="{ 'bg-blue-600 text-white border-blue-600': link.active, 'text-gray-600': !link.active }"
      preserve-state
    />
  </div>
</template>
