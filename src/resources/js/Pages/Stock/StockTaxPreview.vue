<script setup>
const props = defineProps({
  vm: { type: Object, default: null },
})

const money = (value) =>
  Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
</script>

<template>
  <div v-if="!props.vm" class="text-slate-500">
    Selecione o produto e informe o preço de venda.
  </div>

  <div v-else>
    <div class="mb-2">Preço base: <strong>{{ money(props.vm.__totais?.preco_base) }}</strong></div>
    <div class="mb-2">Somente impostos: <strong>{{ money(props.vm.__totais?.total_impostos) }}</strong></div>
    <div class="mb-3">Preço com impostos: <strong>{{ money(props.vm.__totais?.total_com_impostos) }}</strong></div>

    <div v-for="(imp, idx) in props.vm.impostos || []" :key="idx" class="border rounded p-3 mb-3">
      <div class="flex items-center justify-between">
        <div class="font-semibold">
          {{ imp.imposto || imp.codigo || '—' }}
          <span v-if="imp.tax_nome"> - {{ imp.tax_nome }}</span>
        </div>
        <div class="text-right">Total: <strong>{{ money(imp.total) }}</strong></div>
      </div>

      <div v-if="Array.isArray(imp.linhas) && imp.linhas.length" class="mt-2 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-slate-500">
            <tr>
              <th class="text-left pr-4 py-1">Método</th>
              <th class="text-left pr-4 py-1">Base</th>
              <th class="text-left pr-4 py-1">Alíquota/Valor</th>
              <th class="text-right py-1">Imposto</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(linha, j) in imp.linhas" :key="j" class="border-t">
              <td class="pr-4 py-1">{{ linha.metodo_label || '—' }}</td>
              <td class="pr-4 py-1">{{ money(linha.base) }}</td>
              <td class="pr-4 py-1">
                <span v-if="Number(linha.metodo) === 2">{{ money(linha.valor_fixo) }}</span>
                <span v-else>{{ Number(linha.aliquota || 0).toFixed(2) }}%</span>
              </td>
              <td class="text-right py-1">{{ money(linha.valor) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
