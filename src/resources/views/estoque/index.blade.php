@extends('layouts.principal')
@section('conteudo')
  <div class="bg-white p-4 rounded-md w-full">
    <!-- Cabeçalho e botões -->
    <div class="flex justify-end mb-2">
      <a href="{{ route('estoque.cadastro') }}"
        class="md:w-auto flex items-center justify-between px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>
        Cadastrar Estoque
      </a>
    </div>

    <!-- Filtro -->
    <div x-data="{ filterOpen: false }" class="mb-4">
      <button @click="filterOpen = !filterOpen"
        class="w-full md:w-auto flex items-center justify-between px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md">
        <span class="mr-2">Filtrar</span>
        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': filterOpen }"></i>
      </button>

      <div x-show="filterOpen" class="mt-4 p-4 bg-gray-50 rounded-lg" x-cloak>
        <form action="{{ route('estoque.buscar') }}" method="GET"
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-ref="filterForm">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Código do Produto</label>
            <input type="text" name="cod_produto" placeholder="Ex.: ABC-123"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
              value="{{ request('cod_produto') }}">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
            <input type="text" name="nome_produto" placeholder="Nome do Produto"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
              value="{{ request('nome_produto') }}">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lote</label>
            <input type="text" name="lote" placeholder="Lote"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
              value="{{ request('lote') }}">
          </div>

          <!-- Linha 2 -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
            <input type="text" name="localizacao" placeholder="Localização"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
              value="{{ request('localizacao') }}">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Preço Custo</label>
            <input type="number" step="0.01" name="preco_custo" placeholder="Preço Custo"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
              value="{{ request('preco_custo') }}">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Preço Venda</label>
            <input type="number" step="0.01" name="preco_venda" placeholder="Preço Venda"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
              value="{{ request('preco_venda') }}">
          </div>

          <!-- Linha 3 -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
            <input type="number" name="quantidade" placeholder="Quantidade"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
              value="{{ request('quantidade') }}">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
            <select name="nome_marca"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
              <option value="">Selecione uma Marca</option>
              @foreach ($marcas as $marca)
                <option value="{{ $marca->nome_marca }}" {{ request('nome_marca') == $marca->nome_marca ? 'selected' : '' }}>
                  {{ $marca->nome_marca }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor</label>
            <select name="nome_fornecedor"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
              <option value="">Selecione um Fornecedor</option>
              @foreach ($fornecedores as $fornecedor)
                <option value="{{ $fornecedor->nome_fornecedor }}" {{ request('nome_fornecedor') == $fornecedor->nome_fornecedor ? 'selected' : '' }}>
                  {{ $fornecedor->nome_fornecedor }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Linha 4 -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
            <select name="nome_categoria"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
              <option value="">Selecione uma Categoria</option>
              @foreach ($categorias as $categoria)
                <option value="{{ $categoria->nome_categoria }}" {{ request('nome_categoria') == $categoria->nome_categoria ? 'selected' : '' }}>
                  {{ $categoria->nome_categoria }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Botões -->
          <div class="md:col-span-3 flex flex-col sm:flex-row gap-2">
            <button type="submit"
              class="w-full sm:w-auto px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-md transition-colors">
              Aplicar Filtros
            </button>

            <button type="button" @click="$refs.filterForm.reset(); window.location='{{ route('estoque.buscar') }}'"
              class="w-full sm:w-auto px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors">
              Limpar Filtros
            </button>
          </div>
        </form>
      </div>
    </div>


    <!-- Tabela -->
    <div class="overflow-x-auto rounded-lg border">
      <table id="Table" class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Código</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Produto</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Fornecedor</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden md:table-cell">Custo</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden md:table-cell">Venda</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Qtde</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Lote</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden md:table-cell">Local</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Validade</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse ($estoques as $estoque)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-700">
                {{ $estoque->produtos->cod_produto ?? '—' }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-700">
                {{ $estoque->produtos->nome_produto ?? 'N/A' }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-700">
                {{ $estoque->fornecedores->nome_fornecedor ?? 'N/A' }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">
                R$ {{ number_format($estoque->preco_custo, 2, ',', '.') }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">
                R$ {{ number_format($estoque->preco_venda, 2, ',', '.') }}
              </td>
              <td class="px-4 py-3 text-sm font-medium
                  @if($estoque->quantidade <= $estoque->quantidade_aviso) text-red-600 @else text-gray-700 @endif">
                {{ $estoque->quantidade }}
              </td>

              <td class="px-4 py-3 text-sm text-gray-700">{{ $estoque->lote }}</td>
              <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">{{ $estoque->localizacao }}</td>
              <td class="px-4 py-3 text-sm
                  @if(optional($estoque->validade)->isPast()) text-red-600
                  @elseif(optional($estoque->validade)->diffInDays() < 30) text-yellow-600
                  @else text-gray-700 @endif">
                {{ optional($estoque->validade)->format('d/m/Y') }}
              </td>
              <td class="px-4 py-3 text-sm flex gap-2">
                <x-edit-button :route="'estoque.editar'" :modelId="$estoque->id_estoque" menuSlug="estoques" />
                <x-button-status :modelId="$estoque->id_estoque" :status="$estoque->status" modelName="estoque" />
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500">Nenhum registro encontrado.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Paginação -->
    @if(method_exists($estoques, 'links'))
      <div class="mt-4">
        {{ $estoques->appends(request()->except('page'))->links() }}
      </div>
    @endif
  </div>
@endsection