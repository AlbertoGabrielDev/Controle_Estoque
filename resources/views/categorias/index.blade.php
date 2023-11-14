@extends('layouts.principal')

@section('conteudo')

<div class="bg-white p-4 rounded-md w-full">
  <h5 class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center">Index</h5>
  <div class= "bg-white p-4 rounded-md w-full flex justify-between ">
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white"href="{{route('categoria.cadastro')}}">
      <i class="fas fa-plus mr-2"></i>Cadastrar
    </a>
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('categoria.inicio')}}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
  </div>
    <table class="w-full table-auto ">
        <thead>
            <tr class="text-sm leading-normal">
                <th class="py-4 px-6 uppercase text-sm text-grey-dark border-b border-grey-light">Categoria</th>
                <!-- <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Editar</th>
                @can('permissao')
                <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light text-right">Inativar</th>
                @endcan -->
            </tr>
        </thead>
        <tbody>
          @foreach ($categorias as $categoria)
            <tr class="hover:bg-grey-lighter">
                <td class="py-4 px-6 border-b border-grey-light">{{$categoria->nome_categoria}}</td>
                <td class="py-4 px-6 border-b border-grey-light"><a href="{{route('categorias.editar', $categoria->id_categoria)}}" class="btn btn-primary">Editar</a></td>
                <td class="py-4 px-6 border-b border-grey-light text-right">
                @can('permissao')
                  <button class="btn btn-primary toggle-ativacao @if($categoria->status === 1) btn-danger @elseif($categoria->status === 0) btn-success @else btn-primary @endif" data-id="{{ $categoria->id_categoria }}">
                    {{ $categoria->status ? 'Inativar' : 'Ativar' }}
                  </button>
                @endcan
                </td>
            </tr>
          @endforeach
        </tbody>
    </table>


  </div>    

@endsection
