<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Checkbox from '@/Components/Checkbox.vue'

const props = defineProps({
  requireClient: {
    type: Boolean,
    default: true,
  },
})

const form = useForm({
  require_client: props.requireClient,
})

function submit() {
  form.put(route('configuracoes.vendas.update'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Configuracoes de Vendas" />

  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
      <h1 class="text-2xl font-semibold text-slate-700">Configuracoes de Vendas</h1>
      <p class="text-sm text-slate-500 mt-1">
        Controle se o numero do cliente (WhatsApp) sera obrigatorio ao registrar vendas.
      </p>

      <div class="mt-6 border rounded-lg bg-slate-50 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h2 class="text-sm font-semibold text-slate-700">Obrigar numero do cliente</h2>
          <p class="text-xs text-slate-500 mt-1">
            Quando ativo, o sistema exige o numero do cliente para finalizar a venda.
          </p>
        </div>

        <label class="flex items-center gap-3">
          <Checkbox v-model:checked="form.require_client" />
          <span class="text-sm text-slate-600">
            {{ form.require_client ? 'Ativo' : 'Inativo' }}
          </span>
        </label>
      </div>

      <div class="mt-4 flex items-center gap-3">
        <button
          type="button"
          class="px-4 py-2 rounded-md bg-cyan-600 text-white hover:bg-cyan-700 disabled:opacity-60"
          :disabled="form.processing"
          @click="submit"
        >
          {{ form.processing ? 'Salvando...' : 'Salvar' }}
        </button>
        <span v-if="form.recentlySuccessful" class="text-sm text-emerald-600">
          Configuracao atualizada.
        </span>
      </div>
    </div>
  </div>
</template>
