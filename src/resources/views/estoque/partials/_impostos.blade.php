@php
  $fmt = fn($n) => 'R$ ' . number_format((float) $n, 2, ',', '.');
@endphp

@if(empty($vm))
  <div class="text-slate-500">Selecione o produto e informe o preço de venda.</div>
@else
  <div id="impostos-wrap" data-total-impostos="{{ $vm['__totais']['total_impostos'] ?? 0 }}"
    data-total-com-impostos="{{ $vm['__totais']['total_com_impostos'] ?? 0 }}">
    <div class="mb-2">Preço base: <strong>{{ $fmt($vm['__totais']['preco_base'] ?? 0) }}</strong></div>
    <div class="mb-2">Somente impostos: <strong>{{ $fmt($vm['__totais']['total_impostos'] ?? 0) }}</strong></div>
    <div class="mb-3">Preço com impostos: <strong>{{ $fmt($vm['__totais']['total_com_impostos'] ?? 0) }}</strong></div>

    @foreach(($vm['impostos'] ?? []) as $imp)
      @php
        $codigo = $imp['imposto'] ?? $imp['codigo'] ?? '—';
        $nome = $imp['tax_nome'] ?? $imp['nome'] ?? '';
        $total = $imp['total'] ?? 0;
      @endphp

      <div class="border rounded p-3 mb-3">
        <div class="flex items-center justify-between">
          <div class="font-semibold">
            {{ $codigo }}{{ $nome ? ' — ' . $nome : '' }}
          </div>
          <div class="text-right">Total: <strong>{{ $fmt($total) }}</strong></div>
        </div>

        @if(!empty($imp['linhas']))
          <div class="mt-2 overflow-x-auto">
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
                @foreach($imp['linhas'] as $l)
                  <tr class="border-t">
                    <td class="pr-4 py-1">{{ $l['metodo_label'] ?? '—' }}</td>
                    <td class="pr-4 py-1">R$ {{ number_format((float) ($l['base'] ?? 0), 2, ',', '.') }}</td>
                    <td class="pr-4 py-1">
                      @if(($l['metodo'] ?? 1) === 2)
                        R$ {{ number_format((float) ($l['valor_fixo'] ?? 0), 2, ',', '.') }}
                      @else
                        {{ number_format((float) ($l['aliquota'] ?? 0), 2, ',', '.') }}%
                      @endif
                    </td>
                    <td class="text-right py-1">R$ {{ number_format((float) ($l['valor'] ?? 0), 2, ',', '.') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    @endforeach
  </div>
@endif