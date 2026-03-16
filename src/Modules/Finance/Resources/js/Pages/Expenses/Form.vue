<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  form: { type: Object, required: true },
  centrosCusto: { type: Array, default: () => [] },
  contasContabeis: { type: Array, default: () => [] },
  fornecedores: { type: Array, default: () => [] },
  submitLabel: { type: String, default: '' },
})

defineEmits(['submit'])
</script>

<template>
  <form @submit.prevent="$emit('submit')" class="bg-white p-4 rounded shadow max-w-4xl space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Date') }}</label>
        <input v-model="props.form.data" type="date" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.data" class="text-red-600 text-sm mt-1">{{ props.form.errors.data }}</div>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium">{{ $t('Description') }}</label>
        <input v-model="props.form.descricao" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.descricao" class="text-red-600 text-sm mt-1">{{ props.form.errors.descricao }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Amount') }}</label>
        <input v-model="props.form.valor" type="number" step="0.01" min="0" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.valor" class="text-red-600 text-sm mt-1">{{ props.form.errors.valor }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">{{ $t('Cost Center') }}</label>
        <select v-model="props.form.centro_custo_id" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">{{ $t('Select') }}</option>
          <option v-for="c in props.centrosCusto" :key="c.id" :value="c.id">
            {{ c.codigo }} - {{ c.nome }}
          </option>
        </select>
        <div v-if="props.form.errors.centro_custo_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.centro_custo_id }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">{{ $t('Accounting Account') }}</label>
        <select v-model="props.form.conta_contabil_id" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">{{ $t('Select') }}</option>
          <option v-for="c in props.contasContabeis" :key="c.id" :value="c.id">
            {{ c.codigo }} - {{ c.nome }}
          </option>
        </select>
        <div v-if="props.form.errors.conta_contabil_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.conta_contabil_id }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Supplier') }}</label>
        <select v-model="props.form.fornecedor_id" class="mt-1 border rounded px-3 py-2 w-full">
          <option value="">—</option>
          <option v-for="f in props.fornecedores" :key="f.id_fornecedor" :value="f.id_fornecedor">
            {{ f.nome_fornecedor || f.razao_social }}
          </option>
        </select>
        <div v-if="props.form.errors.fornecedor_id" class="text-red-600 text-sm mt-1">{{ props.form.errors.fornecedor_id }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">{{ $t('Document') }}</label>
        <input v-model="props.form.documento" class="mt-1 border rounded px-3 py-2 w-full">
        <div v-if="props.form.errors.documento" class="text-red-600 text-sm mt-1">{{ props.form.errors.documento }}</div>
      </div>

      <div>
        <label class="block text-sm font-medium">{{ $t('Active') }}</label>
        <select v-model="props.form.ativo" class="mt-1 border rounded px-3 py-2 w-full">
          <option :value="true">{{ $t('Active') }}</option>
          <option :value="false">{{ $t('Inactive') }}</option>
        </select>
        <div v-if="props.form.errors.ativo" class="text-red-600 text-sm mt-1">{{ props.form.errors.ativo }}</div>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">{{ $t('Notes') }}</label>
      <textarea v-model="props.form.observacoes" rows="3" class="mt-1 border rounded px-3 py-2 w-full"></textarea>
      <div v-if="props.form.errors.observacoes" class="text-red-600 text-sm mt-1">{{ props.form.errors.observacoes }}</div>
    </div>

    <div class="flex justify-end">
      <button :disabled="props.form.processing" class="px-3 py-2 rounded bg-blue-600 text-white">
        {{ submitLabel || $t('Save') }}
      </button>
    </div>
  </form>
</template>
