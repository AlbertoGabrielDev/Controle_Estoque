<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()

const props = defineProps({
  items: {
    type: Array,
    default: () => [],
  },
  total: {
    type: Number,
    default: 0,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  finalizing: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['increment', 'decrement', 'remove', 'finalize'])

const hasItems = computed(() => props.items.length > 0)

function money(value) {
  const currencyLocale = locale.value === 'en' ? 'en-US' : (locale.value === 'es' ? 'es-ES' : 'pt-BR')
  const currencySymbol = locale.value === 'en' ? 'USD' : (locale.value === 'es' ? 'EUR' : 'BRL')
  
  return Number(value || 0).toLocaleString(currencyLocale, { 
    style: 'currency', 
    currency: currencySymbol 
  })
}
</script>

<template>
  <section class="mt-8 border rounded-lg p-4 bg-white">
    <div class="flex items-center justify-between gap-4">
      <h2 class="text-xl font-bold">{{ $t('Shopping Cart') }}</h2>
      <button
        type="button"
        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg disabled:opacity-60"
        :disabled="loading || finalizing || !hasItems"
        @click="$emit('finalize')"
      >
        {{ finalizing ? $t('Finalizing...') : $t('Finalize Sale') }}
      </button>
    </div>

    <div v-if="!hasItems" class="text-center py-6 text-gray-500">
      {{ $t('No products added to cart.') }}
    </div>

    <div v-else>
      <div class="md:hidden space-y-3 mt-4">
        <article
          v-for="item in items"
          :key="item.rowKey"
          class="border rounded-lg p-3"
        >
          <div class="flex justify-between gap-3">
            <h3 class="font-medium text-gray-800">{{ item.name }}</h3>
            <div class="font-semibold text-gray-800">{{ money(item.subtotal) }}</div>
          </div>
          <div class="mt-1 text-sm text-gray-600">{{ $t('Unit Price:') }} {{ money(item.unitPrice) }}</div>
          <div class="mt-3 flex items-center justify-between">
            <div class="inline-flex items-center border rounded-md overflow-hidden">
              <button
                type="button"
                class="px-3 py-2"
                :disabled="loading || finalizing"
                @click="$emit('decrement', item)"
              >
                -
              </button>
              <span class="px-4 py-2 select-none">{{ item.quantity }}</span>
              <button
                type="button"
                class="px-3 py-2"
                :disabled="loading || finalizing"
                @click="$emit('increment', item)"
              >
                +
              </button>
            </div>
            <button
              type="button"
              class="text-red-600 hover:text-red-700"
              :disabled="loading || finalizing"
              @click="$emit('remove', item)"
            >
              {{ $t('Remove') }}
            </button>
          </div>
        </article>
      </div>

      <div class="hidden md:block overflow-auto mt-4">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Product') }}</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Price') }}</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Quantity') }}</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Subtotal') }}</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Actions') }}</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in items" :key="item.rowKey">
              <td class="px-4 py-3">{{ item.name }}</td>
              <td class="px-4 py-3">{{ money(item.unitPrice) }}</td>
              <td class="px-4 py-3">
                <div class="inline-flex items-center border rounded-md overflow-hidden">
                  <button
                    type="button"
                    class="px-3 py-1"
                    :disabled="loading || finalizing"
                    @click="$emit('decrement', item)"
                  >
                    -
                  </button>
                  <span class="px-4 py-1 select-none">{{ item.quantity }}</span>
                  <button
                    type="button"
                    class="px-3 py-1"
                    :disabled="loading || finalizing"
                    @click="$emit('increment', item)"
                  >
                    +
                  </button>
                </div>
              </td>
              <td class="px-4 py-3">{{ money(item.subtotal) }}</td>
              <td class="px-4 py-3">
                <button
                  type="button"
                  class="text-red-600 hover:text-red-700"
                  :disabled="loading || finalizing"
                  @click="$emit('remove', item)"
                >
                  {{ $t('Remove') }}
                </button>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="px-4 py-4 text-right font-semibold">{{ $t('Cart Total:') }}</td>
              <td class="px-4 py-4 font-bold">{{ money(total) }}</td>
              <td />
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>
</template>
