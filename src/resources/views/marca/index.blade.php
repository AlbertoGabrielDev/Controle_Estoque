@extends('layouts.principal')
@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <!-- Cabeçalho -->
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Marcas</h2>
    <div class="flex gap-4">
      <a href="{{route('categoria.inicio')}}"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-angle-left mr-2"></i>Voltar
      </a>
      <a href="{{route('marca.cadastro')}}"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Cadastrar
      </a>
    </div>
  </div>

  <!-- Busca -->
  <!-- <form action="{{ route('marca.buscar') }}" method="GET" class="mb-6">
    <div class="flex gap-2 w-full md:w-1/2">
      <input type="text"
        name="nome_marca"
        class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-cyan-500"
        placeholder="Digite o nome da Marca">
      <button type="submit"
        class="px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-full transition-colors">
        Pesquisar
      </button>
    </div>
  </form> -->

  <!-- Tabela -->
  <div class="overflow-x-auto rounded-lg border">
    <table id="Table" class="w-full" data-order='[[0, "asc"]]'>
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Marca</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse ($marcas as $marca)
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-sm text-gray-700">{{$marca->nome_marca}}</td>
          <td class="px-4 py-3 text-sm flex gap-2">
            <x-edit-button :route="'marca.editar'" :modelId="$marca->id_marca" />
            <x-button-status
              :modelId="$marca->id_marca"
              :status="$marca->status"
              modelName="marca" />
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="2" class="px-4 py-6 text-center text-gray-500">
            Nenhuma marca encontrada.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection