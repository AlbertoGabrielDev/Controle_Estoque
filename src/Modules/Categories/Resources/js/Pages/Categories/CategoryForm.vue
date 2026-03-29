<script setup>
const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, default: '' },
  showImage: { type: Boolean, default: false },
  categoriasPai: { type: Array, default: () => [] },
})

defineEmits(['submit'])

function onImageChange(event) {
  const file = event.target.files?.[0] ?? null
  props.form.imagem = file
}
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow max-w-2xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Code') }}</label>
        <input v-model="props.form.codigo" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.codigo" class="text-red-600 text-sm mt-1">{{ props.form.errors.codigo }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">{{ $t('Type') }}</label>
        <select v-model="props.form.tipo" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="produto">{{ $t('Products') }}</option>
          <option value="cliente">{{ $t('Customers') }}</option>
          <option value="fornecedor">{{ $t('Suppliers') }}</option>
        </select>
        <div v-if="props.form.errors.tipo" class="text-red-600 text-sm mt-1">{{ props.form.errors.tipo }}</div>
      </div>
    </div>

    <div class="mt-4">
      <label class="block text-sm font-medium">{{ $t('Category Name') }}</label>
      <input v-model="props.form.nome_categoria" class="mt-1 border rounded px-3 py-2 w-full">
      <div v-if="props.form.errors.nome_categoria" class="text-red-600 text-sm mt-1">{{ props.form.errors.nome_categoria }}</div>
    </div>

    <div class="mt-4">
      <label class="block text-sm font-medium">{{ $t('Parent Category') }}</label>
      <select v-model="props.form.categoria_pai_id" class="mt-1 border rounded px-3 py-2 w-full">
        <option value="">{{ $t('—') }}</option>
        <option v-for="c in props.categoriasPai" :key="c.id_categoria" :value="c.id_categoria">
          {{ c.nome_categoria }}
        </option>
      </select>
      <div v-if="props.form.errors.categoria_pai_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.categoria_pai_id }}</div>
    </div>

    <div class="mt-4">
      <label class="block text-sm font-medium">{{ $t('Active') }}</label>
      <select v-model="props.form.ativo" class="mt-1 border rounded px-3 py-2 w-full">
        <option :value="true">{{ $t('Active') }}</option>
        <option :value="false">{{ $t('Inactive') }}</option>
      </select>
      <div v-if="props.form.errors.ativo" class="text-red-600 text-sm mt-1">{{ props.form.errors.ativo }}</div>
    </div>

    <div v-if="showImage" class="mt-4">
      <label class="block text-sm font-medium">{{ $t('Image') }}</label>
      <input type="file" class="mt-1 border rounded px-3 py-2 w-full" @change="onImageChange">
      <div v-if="props.form.errors.imagem" class="text-red-600 text-sm mt-1">{{ props.form.errors.imagem }}</div>
    </div>

    <div class="mt-4 flex justify-end gap-2">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">
        {{ submitLabel || $t('Save') }}
      </button>
    </div>
  </form>
</template>
