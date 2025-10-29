<script setup>
const props = defineProps({
  breakdown: { type: Object, required: true },
  // formato esperado:
  // {
  //   total: 12.34,
  //   lines: [
  //     { code:'ICMS', scope:'item', item_id:123, base:100, value:18, label:'ICMS 18%'},
  //     ...
  //   ],
  //   by_code: { ICMS: 18, PIS: 3.5, ... }
  // }
})
</script>

<template>
  <div class="bg-white rounded shadow p-4">
    <h3 class="text-lg font-semibold mb-3 text-slate-700">Impostos</h3>

    <table class="w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Imposto</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 hidden md:table-cell">Escopo</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 hidden lg:table-cell">Base</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Valor</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <tr v-for="(l, i) in breakdown.lines" :key="i" class="hover:bg-gray-50">
          <td class="px-4 py-2 text-sm text-gray-700">
            {{ l.label || l.code }}
          </td>
          <td class="px-4 py-2 text-sm text-gray-700 hidden md:table-cell">
            {{ l.scope }}
          </td>
          <td class="px-4 py-2 text-sm text-gray-700 hidden lg:table-cell">
            {{ (l.base ?? 0).toLocaleString('pt-BR', { style:'currency', currency:'BRL' }) }}
          </td>
          <td class="px-4 py-2 text-sm text-gray-700">
            {{ (l.value ?? 0).toLocaleString('pt-BR', { style:'currency', currency:'BRL' }) }}
          </td>
        </tr>
        <tr v-if="(breakdown.lines?.length || 0) === 0">
          <td colspan="4" class="px-4 py-4 text-center text-gray-500">Sem impostos aplicáveis.</td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="border-t">
          <td class="px-4 py-3 text-sm font-medium text-gray-700" colspan="3">Total de Impostos</td>
          <td class="px-4 py-3 text-sm font-semibold">
            {{ (breakdown.total ?? 0).toLocaleString('pt-BR', { style:'currency', currency:'BRL' }) }}
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>
