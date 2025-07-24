@extends('layouts.principal')
@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <!-- Cabeçalho e botões... (mantido igual) -->
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
      <form action="{{ route('estoque.buscar') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Data de Chegada</label>
            <div class="relative">
              <input
                type="date"
                name="data_chegada"
                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
                value="{{ request('data_chegada') }}">
            </div>
          </div>
        </div>
        <div>
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Data de Validade</label>
            <div class="relative">
              <input
                type="date"
                name="validade"
                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
                value="{{ request('validade') }}">
            </div>
          </div>
        </div>
        <div>
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Data de Cadastro</label>
            <div class="relative">
              <input
                type="date"
                name="data_cadastro"
                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
                value="{{ request('data_cadastro') }}">
            </div>
          </div>
        </div>
        <!-- Linha 1 -->
        <div>
          <input type="text" name="nome_produto" placeholder="Nome do Produto"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
            value="{{ request('nome_produto') }}">
        </div>
        <div>
          <input type="text" name="lote" placeholder="Lote"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
            value="{{ request('lote') }}">
        </div>
        <div>
          <input type="text" name="localizacao" placeholder="Localização"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
            value="{{ request('localizacao') }}">
        </div>

        <!-- Linha 2 -->
        <div>
          <input type="number" step="0.01" name="preco_custo" placeholder="Preço Custo"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
            value="{{ request('preco_custo') }}">
        </div>
        <div>
          <input type="number" step="0.01" name="preco_venda" placeholder="Preço Venda"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
            value="{{ request('preco_venda') }}">
        </div>

        <div>
          <input type="number" name="quantidade" placeholder="Quantidade"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
            value="{{ request('quantidade') }}">
        </div>
        <div>
          <select name="nome_marca" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
            <option value="">Selecione uma Marca</option>
            @foreach ($marcas as $marca)
            <option value="{{ $marca->nome_marca }}" {{ request('nome_marca') == $marca->nome_marca ? 'selected' : '' }}>
              {{ $marca->nome_marca }}
            </option>
            @endforeach
          </select>
        </div>

        <!-- Linha 4 -->
        <div>
          <select name="nome_fornecedor" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
            <option value="">Selecione um Fornecedor</option>
            @foreach ($fornecedores as $fornecedor)
            <option value="{{ $fornecedor->nome_fornecedor }}" {{ request('nome_fornecedor') == $fornecedor->nome_fornecedor ? 'selected' : '' }}>
              {{ $fornecedor->nome_fornecedor }}
            </option>
            @endforeach
          </select>
        </div>
        <div>
          <select name="nome_categoria" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
            <option value="">Selecione uma Categoria</option>
            @foreach ($categorias as $categoria)
            <option value="{{ $categoria->nome_categoria }}" {{ request('nome_categoria') == $categoria->nome_categoria ? 'selected' : '' }}>
              {{ $categoria->nome_categoria }}
            </option>
            @endforeach
          </select>
        </div>

        <!-- Botão -->
        <div class="md:col-span-3">
          <button type="submit" class="w-full md:w-auto px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-md transition-colors">
            Aplicar Filtros
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabela -->
  <div class="overflow-x-auto rounded-lg border">
    <table id="Table" class="w-full" data-order='[[9, "asc"]]'>
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Produto</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Fornecedor</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden md:table-cell">Custo</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden md:table-cell">Venda</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Qtde</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden lg:table-cell">Chegada</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden xl:table-cell">Cadastro</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Lote</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden md:table-cell">Local</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Validade</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @foreach ($estoques as $estoque)
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-sm text-gray-700">{{ $estoque->produto->nome_produto ?? 'N/A' }}</td>
          <td class="px-4 py-3 text-sm text-gray-700">{{ $estoque->fornecedor->nome_fornecedor ?? 'N/A' }}</td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">R$ {{number_format($estoque->preco_custo, 2, ',', '.')}}</td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">R$ {{number_format($estoque->preco_venda, 2, ',', '.')}}</td>
          <td class="px-4 py-3 text-sm font-medium 
            @if($estoque->quantidade <= $estoque->quantidade_aviso) text-red-600 @else text-gray-700 @endif">
            {{$estoque->quantidade}}
          </td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden lg:table-cell">
            {{ \Carbon\Carbon::parse($estoque->data_chegada)->format('d/m/Y') }}
          </td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden xl:table-cell">
            {{ $estoque->created_at->format('d/m/Y H:i') }}
          </td>
          <td class="px-4 py-3 text-sm text-gray-700">{{$estoque->lote}}</td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">{{$estoque->localizacao}}</td>
          <td class="px-4 py-3 text-sm 
            @if(\Carbon\Carbon::parse($estoque->validade)->isPast()) text-red-600
            @elseif(\Carbon\Carbon::parse($estoque->validade)->diffInDays() < 30) text-yellow-600
            @else text-gray-700 @endif">
            {{ \Carbon\Carbon::parse($estoque->validade)->format('d/m/Y') }}
          </td>
          <td class="px-4 py-3 text-sm flex gap-2">
            <x-edit-button :route="'estoque.editar'" :modelId="$estoque->id_estoque" />
            <x-button-status
              :modelId="$estoque->id_estoque"
              :status="$estoque->status"
              modelName="estoque" />
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection