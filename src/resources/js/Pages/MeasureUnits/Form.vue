<script setup>
const props = defineProps({
  form: { type: Object, required: true },
  unidadesBase: { type: Array, default: () => [] },
  submitLabel: { type: String, default: 'Salvar' },
})

defineEmits(['submit'])
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow max-w-3xl space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Código</label>
        <input v-model="props.form.codigo" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.codigo" class="text-red-600 text-sm mt-1">{{ props.form.errors.codigo }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Descrição</label>
        <input v-model="props.form.descricao" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.descricao" class="text-red-600 text-sm mt-1">{{ props.form.errors.descricao }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Fator Base</label>
        <input v-model="props.form.fator_base" type="number" step="0.000001" min="0" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.fator_base" class="text-red-600 text-sm mt-1">{{ props.form.errors.fator_base }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Unidade Base</label>
        <select v-model="props.form.unidade_base_id" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">—</option>
          <option v-for="u in props.unidadesBase" :key="u.id" :value="u.id">
            {{ u.codigo }} - {{ u.descricao }}
          </option>
        </select>
        <div v-if="props.form.errors.unidade_base_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.unidade_base_id }}</div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Ativo</label>
      <select v-model="props.form.ativo" class="mt-1 border rounded px-3 py-2 w-full">
        <option :value="true">Ativo</option>
        <option :value="false">Inativo</option>
      </select>
      <div v-if="props.form.errors.ativo" class="text-red-600 text-sm mt-1">{{ props.form.errors.ativo }}</div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">{{ submitLabel }}</button>
    </div>
  </form>
</template>
