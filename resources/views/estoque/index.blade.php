@extends('layouts.principal')
@section('conteudo')

<div class="container d-flex justify-content-between align-items-center">
  <div class="mx-auto">
    <h1 class="card-title">Index Estoque</h1>
  </div>
  <div>
    <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>
  </div>
</div>
<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('estoque.cadastro')}}">Cadastrar Estoque</a>     
</div>
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
            <input type="text" class="w-50" name="nome_produto" placeholder="Nome do Produto">
            <div class="col-md-4">
              <select class="form-control form-control-lg w-50" name="nome_fornecedor" >
                <option value="">Selecione um Fornecedor</option>
                @foreach ($fornecedores as $fornecedor)
                    <option value="{{ $fornecedor->nome_fornecedor }}">{{ $fornecedor->nome_fornecedor}}</option>
                @endforeach
              </select>
              <select class="form-control form-control-lg w-50" name="nome_marca" >
                <option value="">Selecione uma Marca</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->nome_marca}}">{{ $marca->nome_marca}}</option>
                @endforeach
              </select>
              <select class="form-control form-control-lg w-50" name="nome_categoria" >
                <option value="">Selecione uma Categoria</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->nome_categoria}}">{{ $categoria->nome_categoria}}</option>
                @endforeach
              </select>
            </div>
            <input type="text" class="w-50" name="lote" placeholder="Lote">
            <input type="text" class="w-50" name="localizacao" placeholder="Localização">
            <input type="text" class="w-50" name="preco_custo" placeholder="Preço Custo">
            <input type="text" class="w-50" name="preco_venda" placeholder="Preço Venda">
            <input type="text" class="w-50" name="quantidade" placeholder="Quantidade">
            <input type="date" class="w-50" name="data_chegada" placeholder="Quantidade">
          </div>
          <button type="submit">Pesquisar</button>
      </div>
    </div>
  </div>

</form>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Nome Produto</th>
        <th scope="col">Nome Marca</th>
        <th scope="col">Nome Fornecedor</th>
        <th scope="col">Preço Custo</th>
        <th scope="col">Preço Venda</th>
        <th scope="col">Quantidade</th>
        <th scope="col">Data de Chegada</th>
        <th scope="col">Data de Cadastro</th>
        <th scope="col">Lote</th>
        <th scope="col">localização</th>
        <th>X</th>
        <th>Y</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($estoque as $estoques)
      <tr>
          <td>{{$estoques->nome_produto}}</td>
          <td>{{$estoques->nome_marca}}</td>
          <td>{{$estoques->nome_fornecedor}}</td>
          <td>{{$estoques->preco_custo}}</td>
          <td>{{$estoques->preco_venda}}</td>
          <td>{{$estoques->quantidade}}</td>
          <td>{{ \Carbon\Carbon::parse($estoques->data_chegada)->format('d/m/Y') }}</td> 
          <td>{{$estoques->created_at}}</td>
          <td>{{$estoques->lote}}</td>
          <td>{{$estoques->localizacao}}</td>
          <td>Editar</td> 
          <td>Deletar</td>
      </tr>
      @endforeach
    </tbody>
</table>

@endsection