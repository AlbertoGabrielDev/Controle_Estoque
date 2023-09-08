@extends('layouts.principal')
@section('conteudo')

<div class="estoque">Index Estoque</div> 

<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('estoque.cadastro')}}">Cadastrar Estoque</a>     
</div>

<table class="table mt-5">
    <thead>
      <tr>
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
            <td>{{$estoques->preco_custo}}</td>
            <td>{{$estoques->preco_venda}}</td>
            <td>{{$estoques->quantidade}}</td>
            <td>{{$estoques->data_chegada}}</td> 
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