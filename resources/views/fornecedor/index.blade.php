@extends('layouts.principal')
@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <!-- Cabeçalho -->
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Fornecedores</h2>
    <div class="flex gap-4">
      <a href="{{route('categoria.inicio')}}"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-angle-left mr-2"></i>Voltar
      </a>
      <a href="{{route('fornecedor.cadastro')}}"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Cadastrar
      </a>
    </div>
  </div>

  <!-- Busca -->
  <form action="{{ route('fornecedor.buscar') }}" method="GET" class="mb-6">
    <div class="flex gap-2 w-full md:w-1/2">
      <input type="text"
        name="nome_fornecedor"
        class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-cyan-500"
        placeholder="Digite o nome do Fornecedor">
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
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nome</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden md:table-cell">CNPJ</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden lg:table-cell">CEP</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Logradouro</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden xl:table-cell">Bairro</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">N. Casa</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden lg:table-cell">Email</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden md:table-cell">Cidade</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">UF</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @foreach ($fornecedores as $fornecedor)
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-sm text-gray-700">{{$fornecedor->nome_fornecedor}}</td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">{{$fornecedor->cnpj}}</td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden lg:table-cell">{{$fornecedor->cep}}</td>
          <td class="px-4 py-3 text-sm text-gray-700">{{$fornecedor->logradouro}}</td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden xl:table-cell">{{$fornecedor->bairro}}</td>
          <td class="px-4 py-3 text-sm text-gray-700">{{$fornecedor->numero_casa}}</td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden lg:table-cell">{{$fornecedor->email}}</td>
          <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">{{$fornecedor->cidade}}</td>
          <td class="px-4 py-3 text-sm text-gray-700">{{$fornecedor->uf}}</td>
          <td class="px-4 py-3 text-sm flex gap-2">
            <x-edit-button :route="'fornecedor.editar'" :modelId="$fornecedor->id_fornecedor" />
            <x-button-status
              :modelId="$fornecedor->id_fornecedor"
              :status="$fornecedor->status"
              modelName="fornecedor" />
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Paginação -->
  <div class="mt-4">
    {{ $fornecedores->links() }}
  </div>
</div>
@endsection