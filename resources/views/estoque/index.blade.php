@extends('layouts.principal')
@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <h5 class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center">Index</h5>
  <div class= "bg-white p-4 rounded-md w-full flex justify-between">
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('categoria.inicio')}}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('estoque.cadastro')}}">
      <i class="fas fa-plus mr-2"></i>Cadastrar
    </a>
  </div>
  
  <form action="{{ route('estoque.index') }}" method="GET">
    <div class="accordion accordion-flush" id="accordionFlushExample">
      <div class="accordion-item">
        <h2 class="accordion-header" id="flush-headingOne">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
            Filtrar
          </button>
        </h2>
      
          <div class="accordion-body">
              <input type="text" class="form-control form-control-lg w-50 m-1" value="{{ request('nome_produto') }}" name="nome_produto" placeholder="Nome do Produto">
              <input type="text" class="form-control form-control-lg w-50 m-1" value="{{request('lote')}}" name="lote" placeholder="Lote">
              <input type="text" class="form-control form-control-lg w-50 m-1" value="{{request('localizacao')}}" name="localizacao" placeholder="Localização">
              <input type="text" class="form-control form-control-lg w-50 m-1" value="{{request('preco_custo')}}" name="preco_custo" placeholder="Preço Custo">
              <input type="text" class="form-control form-control-lg w-50 m-1" value="{{request('preco_venda')}}" name="preco_venda" placeholder="Preço Venda">
              <input type="text" class="form-control form-control-lg w-50 m-1" value="{{request('quantidade')}}" name="quantidade" placeholder="Quantidade">
              <div class="input-group input-group-lg w-50">
              <span class="input-group-text" id="inputGroup-sizing-lg">Data Vencimento</span>
              <input type="date" name="validade" class="form-control form-control-lg " aria-label="Sizing example input">
              </div>
          </div>
            <div class="col-md-4">
              <select class="form-control form-control-lg w-75 m-1" name="nome_fornecedor" value="{{ request('nome_fornecedor') }}">
                  <option value="">Selecione um Fornecedor</option>
                @foreach ($fornecedores as $fornecedor)
                    <option value="{{ $fornecedor->nome_fornecedor }}">{{ $fornecedor->nome_fornecedor}}</option>
                @endforeach
                </select> 
                <select class="form-control form-control-lg w-75 m-1" name="nome_marca" value="{{request('nome_marca')}}">
                <option value="">Selecione uma Marca</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->nome_marca}}">{{ $marca->nome_marca}}</option>
                @endforeach
                </select>
                <select class="form-control form-control-lg w-75 m-1" value="{{request('nome_categoria')}}" name="nome_categoria">
                <option value="">Selecione uma Categoria</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->nome_categoria}}">{{ $categoria->nome_categoria}}</option>
                @endforeach
                </select>
            </div>
            <button class="btn btn-primary m-1" type="submit">Pesquisar</button>
     
      </div>
    </div>

  </form>

  <table class="w-full table-auto">
      <thead>
        <tr class="text-sm leading-normal">
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Nome Produto</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Preço Custo</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Preço Venda</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Quantidade</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Data de Chegada</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Data de Cadastro</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Lote</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">localização</th>
          <th class="aviso p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Quantidade para aviso</th>  
          <th data-order="asc" data-col="data_validade" class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Data de Validade</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Aumentar</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Diminuir</th>
          <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Editar</th>
          @can('permissao')
            <th class="p-4 uppercase text-sm text-grey-dark border-b border-grey-light text-left">Inativar</th>
          @endcan
        </tr>
      </thead>
      <tbody>
        @foreach ($estoquesCollection as $estoque)
        
        <tr class="hover:bg-grey-lighter">
            <td class="p-4 border-b border-grey-light text-left">{{$estoque->nome_produto}}</td>
            <td class="p-4 border-b border-grey-light text-left">R${{$estoque->pivot->preco_custo}}</td>
            <td class="p-4 border-b border-grey-light text-left">R${{$estoque->pivot->preco_venda}}</td>
            <td class="p-4 border-b border-grey-light text-left quantidade" data-quantidade="{{$estoque->quantidade}}">{{$estoque->pivot->quantidade}}</td>
            <td class="p-4 border-b border-grey-light text-left">{{ \Carbon\Carbon::parse($estoque->pivot->data_chegada)->format('d/m/Y') }}</td> 
            <td class="p-4 border-b border-grey-light text-left">{{$estoque->pivot->created_at}}</td>
            <td class="p-4 border-b border-grey-light text-left">{{$estoque->pivot->lote}}</td>
            <td class="p-4 border-b border-grey-light text-left">{{$estoque->pivot->localizacao}}</td>
            <td class="p-4 border-b border-grey-light text-left aviso" data-aviso="{{$estoque->quantidade_aviso}}">{{$estoque->pivot->quantidade_aviso}}</td>
            <td class="p-4 border-b border-grey-light text-left expiration-date" id="data">{{($estoque->pivot->validade)}}</td>
            <td class="border-b border-grey-light">
              <form method="GET" action="{{ route('estoque.quantidade', ['estoqueId' => $estoque->pivot->id_estoque, 'operacao' => 'aumentar']) }}">
                @csrf
                <input type="number" class="text-base placeholder-gray-500 border rounded-full focus:shadow-outline  border-b border-grey-light" name="quantidadeHistorico">
                <button type="submit" class="btn btn-success m-1">Aumentar</button>
            </form>
            </td>
            <td class="border-b border-grey-light">
              <form method="GET" action="{{ route('estoque.quantidade', ['estoqueId' => $estoque->pivot->id_estoque, 'operacao' => 'diminuir']) }}">
                @csrf
                <input type="number" class="text-base placeholder-gray-500 border rounded-full focus:shadow-outline " name="quantidadeHistorico">
                <button type="submit" class="btn btn-success m-1">Diminuir</button>
            </form>
            </td>
            <td class="p-4 border-b border-grey-light text-left"><a href="{{route('estoque.editar', $estoque->pivot->id_estoque)}}">Editar</a></td> 
            <td class="p-4 border-b border-grey-light text-left">
              @can('permissao')
                <button class="toggle-ativacao m-2 @if($estoque->status === 1) btn-danger @elseif($estoque->status === 0) btn-success @else btn-primary @endif" 
                  data-id="{{ $estoque->pivot->id_estoque }}">
                  {{ $estoque->status ? 'Inativar' : 'Ativar' }}
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
        <a href="{{$produtos->previousPageUrl()}}" class="block py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 ">
          <span class="sr-only">Previous</span>
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
        </a>
      </li>
      <li>
        <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700  dark:text-gray-400">{{$produtos->currentPage()}}</a>
      </li>
      <li>
        <a href="{{$produtos->nextPageUrl()}}" class="block py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400">
          <span class="sr-only">Next</span>
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
        </a>
      </li>
    </ul>
  </nav>
  </div>
</div>
@endsection