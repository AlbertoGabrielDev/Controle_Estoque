@extends('layouts.principal')
@section('conteudo')

  <h1 class="h1 text-center m-5">Index Estoque</h1>
  <a class="btn btn-primary m-3" href="{{route('categoria.inicio')}}">Voltar</a>
  <a class="btn btn-primary m-3" href="{{route('estoque.cadastro')}}">Cadastrar Estoque</a>     

<form action="{{ route('estoque.buscar') }}" method="GET">
  <div class="accordion accordion-flush" id="accordionFlushExample">
    <div class="accordion-item">
      <h2 class="accordion-header" id="flush-headingOne">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
          Filtrar
        </button>
      </h2>
      <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
        <div class="accordion-body">
            <input type="text" class="form-control form-control-lg w-50 m-1" name="nome_produto" placeholder="Nome do Produto">
            <input type="text" class="form-control form-control-lg w-50 m-1" name="lote" placeholder="Lote">
            <input type="text" class="form-control form-control-lg w-50 m-1" name="localizacao" placeholder="Localização">
            <input type="text" class="form-control form-control-lg w-50 m-1" name="preco_custo" placeholder="Preço Custo">
            <input type="text" class="form-control form-control-lg w-50 m-1" name="preco_venda" placeholder="Preço Venda">
            <input type="text" class="form-control form-control-lg w-50 m-1" name="quantidade" placeholder="Quantidade">
            <div class="input-group input-group-lg w-50">
            <span class="input-group-text" id="inputGroup-sizing-lg">Data Chegada</span>
            <input type="date" name="data_chegada" class="form-control form-control-lg " aria-label="Sizing example input">
            </div>
          </div>
          <div class="col-md-4">
            <select class="form-control form-control-lg w-75 m-1" name="nome_fornecedor" >
                <option value="">Selecione um Fornecedor</option>
               @foreach ($fornecedores as $fornecedor)
                   <option value="{{ $fornecedor->nome_fornecedor }}">{{ $fornecedor->nome_fornecedor}}</option>
               @endforeach
             </select> 
              <select class="form-control form-control-lg w-75 m-1" name="nome_marca" >
               <option value="">Selecione uma Marca</option>
               @foreach ($marcas as $marca)
                   <option value="{{ $marca->nome_marca}}">{{ $marca->nome_marca}}</option>
               @endforeach
             </select>
             <select class="form-control form-control-lg w-75 m-1" name="nome_categoria" >
               <option value="">Selecione uma Categoria</option>
               @foreach ($categorias as $categoria)
                   <option value="{{ $categoria->nome_categoria}}">{{ $categoria->nome_categoria}}</option>
               @endforeach
             </select>
           </div>
          <button class="btn btn-primary m-1" type="submit">Pesquisar</button>
      </div>
    </div>
  </div>

</form>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Nome Produto</th>
        <th scope="col">Preço Custo</th>
        <th scope="col">Preço Venda</th>
        <th scope="col">Quantidade</th>
        <th scope="col">Data de Chegada</th>
        <th scope="col">Data de Cadastro</th>
        <th scope="col">Lote</th>
        <th scope="col">localização</th>
        <th scope="col">Quantidade para aviso</th>  
        <th scope="col">Aumentar</th>
        <th scope="col">Diminuir</th>
        <th>Editar</th>
        @can('permissao')
          <th>Inativar</th>
        @endcan
      </tr>
    </thead>
    <tbody>
      @foreach ($estoques as $estoque)
      <tr>
      {{-- {{dd($estoque->pivotParent)}}  --}}
            {{-- @foreach ($estoque->produtos as $produto)
            <td>{{$produto->nome_produto}}</td>
          @endforeach --}}
          <td>{{$estoque->pivotParent->nome_produto}}</td>
          <td>{{$estoque->preco_custo}}</td>
          <td>{{$estoque->preco_venda}}</td>
          <td class="quantidade" data-quantidade={{$estoque->quantidade}}>{{$estoque->quantidade}}</td>
          <td>{{ \Carbon\Carbon::parse($estoque->data_chegada)->format('d/m/Y') }}</td> 
          <td>{{$estoque->created_at}}</td>
          <td>{{$estoque->lote}}</td>
          <td>{{$estoque->localizacao}}</td>
          <td class="quantidade_aviso" id="aviso" data-aviso={{$estoque->quantidade_aviso}}>{{$estoque->quantidade_aviso}}</td>
          <td>
            <form method="GET" action="{{ route('estoque.quantidade', ['estoqueId' => $estoque->id_estoque, 'operacao' => 'aumentar']) }}">
              @csrf
              <input type="number" class="form-control form-control-lg m-1" name="quantidadeHistorico">
              <button type="submit" class="btn btn-success m-1">Aumentar</button>
          </form>
          </td>
          <td>
            <form method="GET" action="{{ route('estoque.quantidade', ['estoqueId' => $estoque->id_estoque, 'operacao' => 'diminuir']) }}">
              @csrf
              <input type="number" class="form-control form-control-lg m-1" name="quantidadeHistorico">
              <button type="submit" class="btn btn-success m-1">Diminuir</button>
          </form>
          </td>
          <td><a href="{{route('estoque.editar', $estoque->id_estoque)}}" class="btn btn-primary m-2">Editar</a></td> 
          <td>
            @can('permissao')
              <button class="btn btn-primary toggle-ativacao m-2 @if($estoque->status === 1) btn-danger @elseif($estoque->status === 0) btn-success @else btn-primary @endif" 
                data-id="{{ $estoque->id_estoque }}">
                {{ $estoque->status ? 'Inativar' : 'Ativar' }}
              </button>
            @endcan
          </td>
      </tr>
      @endforeach
    </tbody>
</table>
<nav class="Page navigation example">
  <ul class="pagination">
    {{ $produtos->links()}}
  </ul>
</nav>
@endsection