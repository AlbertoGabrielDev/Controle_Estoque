@extends('layouts.principal')

@section('conteudo')
<div class="container d-flex justify-content-between align-items-center">
  <div class="mx-auto">
    <h1 class="card-title">Index Fornecedores</h1>
  </div>
  <div>
    <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>
  </div>
</div>

<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('fornecedor.cadastro')}}">Cadastrar Fornecedor</a>     
</div>


<form action="{{ route('fornecedor.buscar') }}" method="GET">
  <input type="text" name="nome_fornecedor" placeholder="Digite nome do fornecedor">
  <button type="submit">Pesquisar</button>
</form>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Nome</th>
        <th>X</th>
        <th>Y</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($fornecedores as $fornecedor)
        <tr>
          <td>{{$fornecedor->nome_fornecedor}}</td>
          <td>Editar</td> 
          <td>Deletar</td>
        </tr>
      @endforeach
    </tbody>
</table>
@endsection