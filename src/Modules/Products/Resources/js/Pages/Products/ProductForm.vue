<script setup>
const props = defineProps({
  form: { type: Object, required: true },
  categorias: { type: Array, default: () => [] },
  unidades: { type: Array, default: () => [] },
  itens: { type: Array, default: () => [] },
  submitLabel: { type: String, default: '' },
})

defineEmits(['submit'])
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Product Code') }}</label>
        <input v-model="props.form.cod_produto" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.cod_produto" class="text-red-600 text-sm mt-1">{{ props.form.errors.cod_produto }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">{{ $t('Product Name') }}</label>
        <input v-model="props.form.nome_produto" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.nome_produto" class="text-red-600 text-sm mt-1">{{ props.form.errors.nome_produto }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Description') }}</label>
        <input v-model="props.form.descricao" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.descricao" class="text-red-600 text-sm mt-1">{{ props.form.errors.descricao }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">{{ $t('Measurement Unit') }}</label>
        <select v-model="props.form.unidade_medida_id" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">{{ $t('Select a unit') }}</option>
          <option v-for="u in props.unidades" :key="u.id" :value="u.id">
            {{ u.codigo }} - {{ u.descricao }}
          </option>
        </select>
        <div v-if="props.form.errors.unidade_medida_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.unidade_medida_id }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Category') }}</label>
        <select v-model="props.form.id_categoria_fk" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">{{ $t('Select a category') }}</option>
          <option v-for="cat in props.categorias" :key="cat.id_categoria" :value="cat.id_categoria">
            {{ cat.nome_categoria }}
          </option>
        </select>
        <div v-if="props.form.errors.id_categoria_fk" class="text-red-600 text-sm mt-1">{{ props.form.errors.id_categoria_fk }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">{{ $t('Item (Price Table)') }}</label>
        <select v-model="props.form.item_id" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">{{ $t('—') }}</option>
          <option v-for="item in props.itens" :key="item.id" :value="item.id">
            {{ item.sku }} - {{ item.nome }}
          </option>
        </select>
        <div v-if="props.form.errors.item_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.item_id }}</div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">{{ $t('Nutritional Information (JSON - list of items)') }}</label>
      <textarea
        v-model="props.form.inf_nutriente"
        rows="5"
        class="mt-1 border rounded px-3 py-2 w-full"
        :placeholder="$t('JSON Example: ') + '[{&quot;label&quot;:&quot;Calorias&quot;,&quot;valor&quot;:120,&quot;unidade&quot;:&quot;kcal&quot;},{&quot;label&quot;:&quot;Proteina&quot;,&quot;valor&quot;:5,&quot;unidade&quot;:&quot;g&quot;}]'"
      />
      <div v-if="props.form.errors.inf_nutriente" class="text-red-600 text-sm mt-1">{{ props.form.errors.inf_nutriente }}</div>
    </div>

    <div class="flex justify-end gap-2">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">
        {{ submitLabel || $t('Save') }}
      </button>
    </div>
  </form>
</template>
