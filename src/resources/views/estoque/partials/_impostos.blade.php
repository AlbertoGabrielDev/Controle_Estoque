@php
  $fmt = fn($n) => 'R$ ' . number_format((float) $n, 2, ',', '.');
@endphp

@if(empty($vm))
 
  <div class="text-slate-500">Selecione o produto e informe o preço de venda.</div>
@else
  <div 
    id="impostos-wrap"
    data-total-impostos="{{ $vm['__totais']['total_impostos'] ?? 0 }}"
    data-total-com-impostos="{{ $vm['__totais']['total_com_impostos'] ?? 0 }}"
  >
    <div class="mb-2">Preço base: <strong>{{ $fmt($vm['__totais']['preco_base'] ?? 0) }}</strong></div>
    <div class="mb-2">Somente impostos: <strong>{{ $fmt($vm['__totais']['total_impostos'] ?? 0) }}</strong></div>
    <div class="mb-3">Preço com impostos: <strong>{{ $fmt($vm['__totais']['total_com_impostos'] ?? 0) }}</strong></div>

    @foreach(($vm['impostos'] ?? []) as $imp)
   
      <div class="mb-4">
        <div class="mb-1">
          <span class="font-semibold">
            {{ $imp['codigo'] }}
            @if(!empty($imp['nome']) && $imp['nome'] !== $imp['codigo']) — {{ $imp['nome'] }} @endif
          </span>
          — Total: <strong>{{ $fmt($imp['total'] ?? 0) }}</strong>
        </div>

        <div class="overflow-x-auto rounded border">
          <table class="min-w-full text-xs md:text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-2 py-1 text-left">Regra</th>
                <th class="px-2 py-1 text-left">Método</th>
                <th class="px-2 py-1 text-left">Alíquota/Valor</th>
                <th class="px-2 py-1 text-left">Base</th>
                <th class="px-2 py-1 text-left">Imposto</th>
                <th class="px-2 py-1 text-left">Filtros</th>
                <th class="px-2 py-1 text-left">Vigência</th>
              </tr>
            </thead>
            <tbody>
              @foreach(($imp['linhas'] ?? []) as $l)
                <tr class="border-t">
                  <td class="px-2 py-1">
                    #{{ $l['rule_id'] ?? '' }}
                    @if(!empty($l['cumul'])) <span class="text-xs text-amber-600">(cumul.)</span>@endif
                  </td>
                  <td class="px-2 py-1">{{ $l['metodo'] ?? '' }}</td>
                  <td class="px-2 py-1">{!! $l['aliqfixo'] ?? '' !!}</td>
                  <td class="px-2 py-1">{!! $l['base'] ?? '' !!}</td>
                  <td class="px-2 py-1 font-medium">{{ $fmt($l['valor'] ?? 0) }}</td>
                  <td class="px-2 py-1">{{ $l['filtros'] ?? '—' }}</td>
                  <td class="px-2 py-1">{{ $l['vig'] ?? '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endforeach
  </div>
@endif
