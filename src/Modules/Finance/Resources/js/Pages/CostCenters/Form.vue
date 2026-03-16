<script setup>
const props = defineProps({
  form: { type: Object, required: true },
  centrosPai: { type: Array, default: () => [] },
  submitLabel: { type: String, default: '' },
})

defineEmits(['submit'])
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow max-w-3xl space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Code') }}</label>
        <input v-model="props.form.codigo" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.codigo" class="text-red-600 text-sm mt-1">{{ props.form.errors.codigo }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">{{ $t('Name') }}</label>
        <input v-model="props.form.nome" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.nome" class="text-red-600 text-sm mt-1">{{ props.form.errors.nome }}</div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">{{ $t('Parent Center') }}</label>
      <select v-model="props.form.centro_pai_id" class="mt-1 border rounded px-3 py-2 w-full">
        <option value="">—</option>
        <option v-for="c in props.centrosPai" :key="c.id" :value="c.id">
          {{ c.codigo }} - {{ c.nome }}
        </option>
      </select>
      <div v-if="props.form.errors.centro_pai_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.centro_pai_id }}</div>
    </div>

    <div>
      <label class="block text-sm font-medium">{{ $t('Active') }}</label>
      <select v-model="props.form.ativo" class="mt-1 border rounded px-3 py-2 w-full">
        <option :value="true">{{ $t('Active') }}</option>
        <option :value="false">{{ $t('Inactive') }}</option>
      </select>
      <div v-if="props.form.errors.ativo" class="text-red-600 text-sm mt-1">{{ props.form.errors.ativo }}</div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">
        {{ submitLabel || $t('Save') }}
      </button>
    </div>
  </form>
</template>
