<script setup>
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import { ref, watch } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  clientesOptions: { type: Array, default: () => [] },
  usersOptions: { type: Array, default: () => [] },
  submitLabel: { type: String, default: 'Save' },
})

const emit = defineEmits(['submit'])

const selectedCliente = ref(
  props.clientesOptions.find(c => c.id_cliente === props.form.cliente_id) ?? null
)
const selectedResponsavel = ref(
  props.usersOptions.find(u => u.id === props.form.responsavel_id) ?? null
)

function clienteLabel(c) {
  return c.nome_fantasia || c.razao_social || c.nome || ''
}

watch(selectedCliente, (val) => { props.form.cliente_id = val?.id_cliente ?? '' })
watch(selectedResponsavel, (val) => { props.form.responsavel_id = val?.id ?? '' })
</script>

<template>
  <form @submit.prevent="emit('submit')" class="bg-white p-4 rounded shadow dark:bg-slate-900 dark:border dark:border-slate-700 space-y-4 text-slate-700 dark:text-slate-100">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">{{ $t('Name') }} *</label>
        <input v-model="form.nome" type="text" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" required>
        <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">{{ form.errors.nome }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Customer') }}</label>
        <Multiselect
          v-model="selectedCliente"
          :options="clientesOptions"
          :custom-label="clienteLabel"
          track-by="id_cliente"
          :placeholder="$t('Select customer')"
          select-label=""
          deselect-label=""
          class="mt-1"
        />
        <div v-if="form.errors.cliente_id" class="text-red-600 text-sm mt-1">{{ form.errors.cliente_id }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Origin') }}</label>
        <input v-model="form.origem" type="text" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100" :placeholder="$t('e.g. Inbound, Referral')">
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Responsible') }}</label>
        <Multiselect
          v-model="selectedResponsavel"
          :options="usersOptions"
          label="name"
          track-by="id"
          :placeholder="$t('Select responsible')"
          select-label=""
          deselect-label=""
          class="mt-1"
        />
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Estimated Value') }}</label>
        <input v-model="form.valor_estimado" type="number" step="0.01" min="0" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      </div>
      <div>
        <label class="block text-sm font-medium">{{ $t('Expected Close Date') }}</label>
        <input v-model="form.data_prevista_fechamento" type="date" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium">{{ $t('Description') }}</label>
        <textarea v-model="form.descricao" rows="2" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100"></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium">{{ $t('Notes') }}</label>
        <textarea v-model="form.observacoes" rows="2" class="mt-1 border rounded px-3 py-2 w-full dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100"></textarea>
      </div>
    </div>

    <div class="flex justify-end">
      <button :disabled="form.processing" type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition-colors">
        {{ submitLabel }}
      </button>
    </div>
  </form>
</template>
