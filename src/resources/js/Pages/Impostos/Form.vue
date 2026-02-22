<script setup>
const props = defineProps({
  form: { type: Object, required: true },
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
        <label class="block text-sm font-medium">Nome</label>
        <input v-model="props.form.nome" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.nome" class="text-red-600 text-sm mt-1">{{ props.form.errors.nome }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Tipo</label>
        <select v-model="props.form.tipo" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="IVA">IVA</option>
          <option value="ISENTO">ISENTO</option>
          <option value="RETENCAO">RETENCAO</option>
          <option value="OUTRO">OUTRO</option>
        </select>
        <div v-if="props.form.errors.tipo" class="text-red-600 text-sm mt-1">{{ props.form.errors.tipo }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">Alíquota (%)</label>
        <input v-model="props.form.aliquota_percent" type="number" step="0.01" min="0" max="100" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.aliquota_percent" class="text-red-600 text-sm mt-1">{{ props.form.errors.aliquota_percent }}</div>
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
