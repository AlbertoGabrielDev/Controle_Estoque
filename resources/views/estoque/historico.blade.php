@extends('layouts.principal')
@section('conteudo')

<div class="bg-white p-4 rounded-md w-full">
  <h5 class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center">Index</h5>
  <div class= "bg-white p-4 rounded-md w-full flex justify-between">
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('categoria.inicio')}}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
  </div>
 
  <table class="w-full table-auto" id="historico-list">
      <thead>
        <tr class="text-sm leading-normal">
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Produto</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Marca</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Fornecedor</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Quantidade retirada</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Quantidade</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Data de alteração</th>
          {{-- <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Usuário que altero</th> --}}
        </tr>
      </thead>
      <tbody>
        @foreach ($historicos as $historico)
        <tr class="hover:bg-grey-lighter">
          @foreach ($historico->estoques->produtos as $produto)
            <td class="p-4 border-b border-grey-light text-left">{{$produto->nome_produto}}</td>
          @endforeach
          @foreach ($historico->estoques->marcas as $marca)
            <td class="p-4 border-b border-grey-light text-left">{{$marca->nome_marca}}</td>
          @endforeach
          @foreach ($historico->estoques->fornecedores as $fornecedor)
            <td class="p-4 border-b border-grey-light text-left">{{$fornecedor->nome_fornecedor}}</td>
          @endforeach
            <td class="p-4 border-b border-grey-light text-left">{{$historico->quantidade_diminuida}}</td>
            <td class="p-4 border-b border-grey-light text-left">{{$historico->quantidade_historico}}</td>
            <td class="p-4 border-b border-grey-light text-left">{{ \Carbon\Carbon::parse($historico->updated_at)->format('d/m/Y h:i:s A') }}</td> 
            {{-- <td class="p-4 border-b border-grey-light text-left">{{$historico->estoques->id_users_fk}}</td> --}}
        </tr>
        @endforeach
      </tbody>
  </table>
</div>
@endsection