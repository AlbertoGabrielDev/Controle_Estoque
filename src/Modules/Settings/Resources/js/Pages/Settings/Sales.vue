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
  <Head :title="$t('Sales Configuration')" />

  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
      <h1 class="text-2xl font-semibold text-slate-700">{{ $t('Sales Configuration') }}</h1>
      <p class="text-sm text-slate-500 mt-1">
        {{ $t('Control whether the customer number (WhatsApp) will be mandatory when recording sales.') }}
      </p>

      <div class="mt-6 border rounded-lg bg-slate-50 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h2 class="text-sm font-semibold text-slate-700">{{ $t('Require customer number') }}</h2>
          <p class="text-xs text-slate-500 mt-1">
            {{ $t('When active, the system requires the customer number to finalize the sale.') }}
          </p>
        </div>

        <label class="flex items-center gap-3">
          <Checkbox v-model:checked="form.require_client" />
          <span class="text-sm text-slate-600">
            {{ form.require_client ? $t('Active') : $t('Inactive') }}
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
          {{ form.processing ? $t('Saving...') : $t('Save') }}
        </button>
        <span v-if="form.recentlySuccessful" class="text-sm text-emerald-600">
          {{ $t('Configuration updated.') }}
        </span>
      </div>
    </div>
  </div>
</template>
