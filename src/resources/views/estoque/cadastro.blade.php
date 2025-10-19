@extends('layouts.principal')

@section('conteudo')
  <div class="bg-white p-4 rounded-md w-full">
    <div class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center">Cadastro de Estoque</div>

    <a class="text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white"
      href="{{ route('estoque.index') }}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>

    <form action="{{ route('estoque.inserirEstoque') }}" method="POST" id="form-estoque">
      @csrf
      <input type="hidden" name="imposto_total" id="imposto_total" value="">
      <input type="hidden" name="impostos_json" id="impostos_json" value="">
      <div class="grid md:grid-cols-3 md:gap-6 py-4">
        <div class="relative z-0 w-full mb-6">
          <input type="number" name="quantidade" value="{{ old('quantidade') }}"
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600"
            required placeholder=" ">
          <label class="text-sm text-gray-500">Quantidade</label>
          @error('quantidade') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="relative z-0 w-full mb-6">
          <input type="text" name="preco_custo" value="{{ old('preco_custo') }}"
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600"
            required placeholder=" ">
          <label class="text-sm text-gray-500">Preço Custo</label>
          @error('preco_custo') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="relative z-0 w-full mb-6">
          <input type="text" name="preco_venda" value="{{ old('preco_venda', $previewInput['preco_venda'] ?? '') }}"
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600"
            required placeholder=" ">
          <label class="text-sm text-gray-500">Preço Venda</label>
          @error('preco_venda') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="grid md:grid-cols-3 md:gap-6 py-4">
        <div class="relative z-0 w-full mb-6">
          <input type="number" name="quantidade_aviso" value="{{ old('quantidade_aviso') }}"
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600"
            required placeholder=" ">
          <label class="text-sm text-gray-500">Quantidade para Aviso</label>
          @error('quantidade_aviso') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="relative z-0 w-full mb-6">
          <input type="text" name="lote" value="{{ old('lote') }}"
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600"
            required placeholder=" ">
          <label class="text-sm text-gray-500">Lote</label>
          @error('lote') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="relative z-0 w-full mb-6">
          <input type="text" name="localizacao" value="{{ old('localizacao') }}"
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600"
            required placeholder=" ">
          <label class="text-sm text-gray-500">Localização</label>
          @error('localizacao') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="grid md:grid-cols-2 md:gap-6 py-4">
        <div>
          <label class="text-sm text-gray-500">Data Vencimento</label>
          <input type="date" name="validade" value="{{ old('validade') }}"
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600"
            required>
          @error('validade') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm text-gray-500">Data Chegada</label>
          <input type="date" name="data_chegada" value="{{ old('data_chegada') }}"
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600"
            required>
          @error('data_chegada') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="grid md:grid-cols-3 md:gap-6 py-4">
        <div>
          <label class="text-sm text-gray-500">Marca</label>
          <select name="id_marca_fk" required
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600">
            <option value="">Selecione uma Marca</option>
            @foreach ($marcas as $marca)
              <option value="{{ $marca->id_marca }}" @selected(old('id_marca_fk') == $marca->id_marca)>
                {{ $marca->nome_marca }}
              </option>
            @endforeach
          </select>
          @error('id_marca_fk') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm text-gray-500">Produto</label>
          <select name="id_produto_fk" required
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600">
            <option value="">Selecione um Produto</option>
            @foreach ($produtos as $produto)
              <option value="{{ $produto->id_produto }}" @selected(old('id_produto_fk', $previewInput['id_produto_fk'] ?? '') == $produto->id_produto)>
                {{ $produto->nome_produto }}
              </option>
            @endforeach
          </select>
          @error('id_produto_fk') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm text-gray-500">Fornecedor</label>
          <select name="id_fornecedor_fk" required
            class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-600">
            <option value="">Selecione um Fornecedor</option>
            @foreach ($fornecedores as $fornecedor)
              <option value="{{ $fornecedor->id_fornecedor }}"
                @selected(old('id_fornecedor_fk') == $fornecedor->id_fornecedor)>
                {{ $fornecedor->nome_fornecedor }}
              </option>
            @endforeach
          </select>
          @error('id_fornecedor_fk') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="mt-6 border rounded p-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-700">Impostos estimados</h3>
        </div>

        <div id="impostosArea" class="mt-3 text-sm text-slate-700">
          @include('estoque.partials._impostos', ['vm' => $previewVM ?? null])
        </div>
      </div>

      <button type="submit"
        class="block text-gray-500 py-2.5 relative my-4 w-48 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white">
        <i class="fas fa-plus mr-2"></i> Criar Produto
      </button>
    </form>
  </div>

  <script>
    window.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('form-estoque');
      const area = document.getElementById('impostosArea');
      const inputImpostoTotal = document.getElementById('imposto_total');
      const inputImpostosJSON = document.getElementById('impostos_json');
      if (!form || !area || !inputImpostoTotal || !inputImpostosJSON) return;
      let debounceTimer = null;
      function val(name) { const el = form.querySelector(`[name="${name}"]`); return el ? el.value : ''; }
      async function recalcular() {
        const produto = val('id_produto_fk');
        const preco = val('preco_venda');
        if (!produto || !preco) {
          area.innerHTML = '<div class="text-slate-500">Selecione o produto e informe o preço de venda.</div>';
          inputImpostoTotal.value = '';
          inputImpostosJSON.value = '';
          return;
        }
        area.innerHTML = '<div class="text-slate-500">Calculando...</div>';
        try {
          const resp = await fetch('{{ route('estoque.calcImpostos') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
              id_produto_fk: parseInt(produto, 10),
              preco_venda: parseFloat(preco)
            })
          });

          if (!resp.ok) throw new Error('Falha ao calcular');
          const data = await resp.json();
          area.innerHTML = data.html || '<div class="text-slate-500">Sem dados.</div>';
          const meta = data.meta || {};
          inputImpostoTotal.value = (meta.total_com_impostos ?? '').toString();
          try { inputImpostosJSON.value = JSON.stringify(data.raw || {}); } catch (e) { inputImpostosJSON.value = ''; }
          if (!meta.total_com_impostos) {
            const wrap = area.querySelector('#impostos-wrap');
            if (wrap && wrap.dataset.totalComImpostos) inputImpostoTotal.value = wrap.dataset.totalComImpostos;
          }
        } catch (e) {
          console.error(e);
          area.innerHTML = '<div class="text-red-600">Erro ao calcular impostos.</div>';
          inputImpostoTotal.value = '';
          inputImpostosJSON.value = '';
        }
      }

      function triggerRecalcDebounced() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(recalcular, 200);
      }
      ['id_produto_fk', 'preco_venda'].forEach(n => {
        const el = form.querySelector(`[name="${n}"]`);
        if (el) {
          el.addEventListener('change', triggerRecalcDebounced);
          el.addEventListener('input', triggerRecalcDebounced);
          el.addEventListener('blur', triggerRecalcDebounced);
        }
      });
      triggerRecalcDebounced();
      form.addEventListener('submit', async function (ev) {
        if (!inputImpostoTotal.value) {
          ev.preventDefault();
          await recalcular();
          if (inputImpostoTotal.value) form.submit();
        }
      });
    });
  </script>
@endsection