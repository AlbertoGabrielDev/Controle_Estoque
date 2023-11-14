@extends('layouts.principal')
@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
<h5 class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center">Index</h5>
  <div class= "bg-white p-4 rounded-md w-full flex justify-between">
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('categoria.inicio')}}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('fornecedor.cadastro')}}">
      <i class="fas fa-plus mr-2"></i>Cadastrar
    </a>
  </div>
  
<form action="{{ route('fornecedor.buscar') }}" method="GET" class="relative w-6/12">
  <div class="relative w-full">
    <input type="text" name="nome_fornecedor" class="w-5/12 h-10 pl-10 text-base placeholder-gray-500 border rounded-full focus:shadow-outline" placeholder="Digite o nome do Fornecedor">
    <button class="w-2/12 h-10 text-base placeholder-gray-500 border rounded-full focus:shadow-outline" type="submit">Pesquisar</button>
  </div>
</form>

<table class="w-full table-auto">
    <thead>
      <tr class="text-sm leading-normal">
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Nome</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">CNPJ</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">CEP</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Logradouro</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Bairro</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">N. Casa</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Email</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Cidade</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">UF</th>
        <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Editar</th>
          @can('permissao')
            <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Inativar</th>
          @endcan
      </tr>
    </thead>
    <tbody>
      @foreach ($fornecedores as $fornecedor)
        <tr class="hover:bg-grey-lighter">
          <td class="p-8 border-b border-grey-light text-left">{{$fornecedor->nome_fornecedor}}</td>
          <td class="p-4 border-b border-grey-light text-left">{{$fornecedor->cnpj}}</td>
          <td class="p-4 border-b border-grey-light text-left">{{$fornecedor->cep}}</td>
          <td class="p-4 border-b border-grey-light text-left">{{$fornecedor->logradouro}}</td>
          <td class="p-4 border-b border-grey-light text-left">{{$fornecedor->bairro}}</td>
          <td class="p-8 border-b border-grey-light text-left">{{$fornecedor->numero_casa}}</td>
          <td class="p-4 border-b border-grey-light text-left">{{$fornecedor->email}}</td>
          <td class="p-8 border-b border-grey-light text-left">{{$fornecedor->cidade}}</td>
          <td class="p-8 border-b border-grey-light text-left">{{$fornecedor->uf}}</td>
          <td class="p-8 border-b border-grey-light text-left"><a href="{{route('fornecedor.editar', $fornecedor->id_fornecedor)}}">Editar</a></td> 
          <td class="p-8 border-b border-grey-light text-left">
            @can('permissao')
              <button class="toggle-ativacao @if($fornecedor->status === 1) btn-danger @elseif($fornecedor->status === 0) btn-success @else btn-primary @endif"" data-id="{{ $fornecedor->id_fornecedor }}">
                {{ $fornecedor->status ? 'Inativar' : 'Ativar' }}
              </button>
            @endcan
          </td> 
        </tr>
      @endforeach
    </tbody>
</table>
  <div class="mx-auto">
    <nav aria-label="Page navigation example">
      <ul class="inline-flex items-center -space-x-px">
        <li>
          <a href="{{$fornecedores->previousPageUrl()}}" class="block py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 ">
            <span class="sr-only">Previous</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
          </a>
        </li>
        <li>
          <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700  dark:text-gray-400">{{$fornecedores->currentPage()}}</a>
        </li>
        <li>
          <a href="{{$fornecedores->nextPageUrl()}}" class="block py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400">
            <span class="sr-only">Next</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</div>
@endsection