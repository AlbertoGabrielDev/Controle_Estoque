@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <h5 class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center">Index</h5>
  <div class= "bg-white p-4 rounded-md w-full flex justify-between">
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('categoria.inicio')}}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('marca.cadastro')}}">
      <i class="fas fa-plus mr-2"></i>Cadastrar
    </a>
  </div>

<form action="{{ route('marca.buscar') }}" class="relative w-6/12" method="GET">
  <div class="relative w-full">
    <input type="text" name="nome_marca" class="w-5/12 h-10 pl-10 text-base placeholder-gray-500 border rounded-full focus:shadow-outline" placeholder="Digite o nome da Marca">
    <button class="w-2/12 h-10 text-base placeholder-gray-500 border rounded-full focus:shadow-outline" type="submit">Pesquisar</button>
  </div>
</form>

<table class="w-full table-auto">
    <thead>
      <tr class="text-sm leading-normal">
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Marca</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Editar</th>
        @can('permissao')
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Inativar</th> 
        @endcan
      </tr>
    </thead>
    <tbody>
      @foreach ($marcas as $marca)
      <tr class="hover:bg-grey-lighter">
        <td class="p-4 border-b border-grey-light text-left">{{$marca->nome_marca}}</td>
        <td class="p-4 border-b border-grey-light text-left"><a href="{{route('marca.editar', $marca->id_marca)}}">Editar</a></td> 
        <td class="p-4 border-b border-grey-light text-left">
          @can('permissao')
            <button class="toggle-ativacao  @if($marca->status === 1) btn-danger @elseif($marca->status === 0) btn-success @else btn-primary @endif"" data-id="{{ $marca->id_marca }}" >
              {{ $marca->status ? 'Inativar' : 'Ativar' }}
            </button>
          @endcan
        </td>
      </tr>
      @endforeach
    </tbody>
</table>
</div>
<nav class="Page navigation example">
  <ul class="pagination">
    {{ $marcas->links()}}
  </ul>
</nav>
@endsection