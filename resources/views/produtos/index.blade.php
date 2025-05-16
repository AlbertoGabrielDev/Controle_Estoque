@extends('layouts.principal')
@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <!-- Cabeçalho -->
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Produtos</h2>
    <div class="flex gap-4">
      <a href="{{route('categoria.inicio')}}"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-angle-left mr-2"></i>Voltar
      </a>
      <a href="{{route('produtos.cadastro')}}"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Cadastrar
      </a>
    </div>
  </div>

  <!-- Busca -->
  <form action="{{ route('produtos.buscar') }}" method="GET" class="mb-6">
    <div class="flex gap-2 w-full md:w-1/2">
      <input type="text"
        name="nome_produto"
        class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-cyan-500"
        placeholder="Digite o nome do Produto">
      <button type="submit"
        class="px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-full transition-colors">
        Pesquisar
      </button>
    </div>
  </form>

  <!-- Tabela -->
  <div class="overflow-x-auto rounded-lg border">
    <table class="w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Código</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nome</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden lg:table-cell">Descrição</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Unidade</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nutrição</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse ($produtos as $produto)
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-sm text-gray-700">{{$produto->cod_produto}}</td>
          <td class="px-4 py-3 text-sm text-gray-700">{{$produto->nome_produto}}</td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden lg:table-cell">{{$produto->descricao}}</td>
          <td class="px-4 py-3 text-sm text-gray-700">{{$produto->unidade_medida}}</td>
          <td class="px-4 py-3 text-sm text-gray-700" x-data="{ reportsOpen: false }">
            <div @click="reportsOpen = !reportsOpen" class="flex items-center cursor-pointer text-cyan-600 hover:text-cyan-700">
              <div class="w-8 transform transition-transform" :class="{ 'rotate-90': reportsOpen }">
                <i class="fas fa-angle-down"></i>
              </div>
              <span class="ml-2">Informações Nutricionais</span>
            </div>
            <div class="pl-6 mt-2 text-gray-500" x-show="reportsOpen" x-collapse>
              {{ json_decode($produto->inf_nutrientes) }}
            </div>
          </td>
          <td class="px-4 py-3 text-sm flex gap-2">
            <x-edit-button :route="'produtos.editar'" :modelId="$produto->id_produto" />
            <x-button-status
              :modelId="$produto->id_produto"
              :status="$produto->status"
              modelName="produto" />
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-4 py-6 text-center text-gray-500">
            Nenhum produto encontrado
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Paginação -->
  <div class="mt-4">
    {{ $produtos->links() }}
  </div>
</div>
@endsection