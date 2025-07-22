@extends('layouts.principal')
@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <!-- Cabeçalho -->
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-slate-700">Unidades</h2>
    <div class="flex gap-4">
      <a href="{{route('categoria.inicio')}}"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-angle-left mr-2"></i>Voltar
      </a>
      <a href="{{route('unidades.cadastro')}}"
        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800 transition-colors">
        <i class="fas fa-plus mr-2"></i>Cadastrar
      </a>
    </div>
  </div>

  <!-- Tabela -->
  <div class="overflow-x-auto rounded-lg border">
    <table id="Table" class="w-full" data-order='[[0, "asc"]]'>
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Unidade</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse ($unidades as $unidade)
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-sm text-gray-700">{{$unidade->nome}}</td>
          <td class="px-4 py-3 text-sm flex gap-2">
            <x-edit-button :route="'unidades.editar'" :modelId="$unidade->id_unidade" />
            <x-button-status
              :modelId="$unidade->id_unidade"
              :status="$unidade->status"
              modelName="unidades" />
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="2" class="px-4 py-6 text-center text-gray-500">
            Nenhuma unidade encontrada.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection