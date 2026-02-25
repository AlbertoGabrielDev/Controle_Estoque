<script setup>
import { watch } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  categorias: { type: Array, default: () => [] },
  unidades: { type: Array, default: () => [] },
  submitLabel: { type: String, default: 'Salvar' },
})

defineEmits(['submit'])

watch(
  () => props.form.tipo,
  (tipo) => {
    if (tipo === 'servico') {
      props.form.controla_estoque = false
    } else if (tipo === 'produto' && props.form.controla_estoque === false) {
      props.form.controla_estoque = true
    }
  },
  { immediate: true }
)
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">SKU</label>
        <input v-model="props.form.sku" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.sku" class="text-red-600 text-sm mt-1">{{ props.form.errors.sku }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Nome</label>
        <input v-model="props.form.nome" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.nome" class="text-red-600 text-sm mt-1">{{ props.form.errors.nome }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Tipo</label>
        <select v-model="props.form.tipo" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="produto">Produto</option>
          <option value="servico">Serviço</option>
        </select>
        <div v-if="props.form.errors.tipo" class="text-red-600 text-sm mt-1">{{ props.form.errors.tipo }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Categoria</label>
        <select v-model="props.form.categoria_id" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">—</option>
          <option v-for="c in props.categorias" :key="c.id_categoria" :value="c.id_categoria">
            {{ c.nome_categoria }}
          </option>
        </select>
        <div v-if="props.form.errors.categoria_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.categoria_id }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Unidade de Medida</label>
        <select v-model="props.form.unidade_medida_id" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">—</option>
          <option v-for="u in props.unidades" :key="u.id" :value="u.id">
            {{ u.codigo }} - {{ u.descricao }}
          </option>
        </select>
        <div v-if="props.form.errors.unidade_medida_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.unidade_medida_id }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Descrição</label>
        <input v-model="props.form.descricao" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.descricao" class="text-red-600 text-sm mt-1">{{ props.form.errors.descricao }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Custo</label>
        <input v-model="props.form.custo" type="number" step="0.01" min="0" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.custo" class="text-red-600 text-sm mt-1">{{ props.form.errors.custo }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Preço Base</label>
        <input v-model="props.form.preco_base" type="number" step="0.01" min="0" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.preco_base" class="text-red-600 text-sm mt-1">{{ props.form.errors.preco_base }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Peso (kg)</label>
        <input v-model="props.form.peso_kg" type="number" step="0.001" min="0" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.peso_kg" class="text-red-600 text-sm mt-1">{{ props.form.errors.peso_kg }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Volume (m³)</label>
        <input v-model="props.form.volume_m3" type="number" step="0.000001" min="0" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.volume_m3" class="text-red-600 text-sm mt-1">{{ props.form.errors.volume_m3 }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="flex items-center gap-2 border rounded px-3 py-2">
        <input v-model="props.form.controla_estoque" type="checkbox">
        <span>Controla Estoque</span>
      </label>

      <div>
        <label class="block text-sm font-medium">Ativo</label>
        <select v-model="props.form.ativo" class="mt-1 border rounded px-3 py-2 w-full">
          <option :value="true">Ativo</option>
          <option :value="false">Inativo</option>
        </select>
        <div v-if="props.form.errors.ativo" class="text-red-600 text-sm mt-1">{{ props.form.errors.ativo }}</div>
      </div>
    </div>

    <div class="flex justify-end gap-2">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">{{ submitLabel }}</button>
    </div>
  </form>
</template>
