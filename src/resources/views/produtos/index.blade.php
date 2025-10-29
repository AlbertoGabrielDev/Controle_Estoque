@extends('layouts.principal')
@section('conteudo')
  <div class="bg-white p-4 rounded-md w-full">
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

    <div class="overflow-x-auto rounded-lg border">
      <table id="Table"
       class="dt-server w-full"
       data-url="{{ route('produtos.data') }}"
       data-order='[[1,"asc"]]'
       data-length="15"
       data-column-defs='[{"targets":5,"orderable":false,"width":"92px","className":"text-right"}]'
       data-columns='[
         {"data":"c1"},
         {"data":"c2"},
         {"data":"c3","className":"hidden lg:table-cell"},
         {"data":"c4"},
         {"data":"c5","orderable":false,"searchable":false},
         {"data":"acoes","orderable":false,"searchable":false}
       ]'>
        <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Código</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nome</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 hidden lg:table-cell">Descrição</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Unidade</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nutrição</th>
          <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 text-right">Ações</th>
        </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
@endsection
