@extends('layouts.principal')

@section('conteudo')

<div class="fornecedor">Index Fornecedores</div> 

<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('fornecedor.cadastro')}}">Cadastrar Fornecedor</a>     
</div>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Nome</th>
        <th>X</th>
        <th>Y</th>
      </tr>
    </thead>
    <tbody>
      <tr>
       @foreach ($fornecedor as $fornecedores)
        <td>  {{$fornecedores->nome_fornecedor}}</td>
       @endforeach
        <td>Editar</td> 
        <td>Deletar</td>
      </tr>
     
    </tbody>
</table>
@endsection