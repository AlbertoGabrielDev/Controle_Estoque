@extends('layouts.principal')

@section('conteudo')
<div class="container d-flex justify-content-between align-items-center">
  <div class="mx-auto">
    <h1 class="card-title">Editar Usuario</h1>
  </div>
  <div>
    <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>
  </div>
</div>
<form action="{{route('usuario.salvarEditar', $usuario->first()->id)}}" method="POST">
  @csrf
  @foreach ($produtos as $produto)
    <div class="input-group input-group-lg">
      <span class="input-group-text" id="inputGroup-sizing-lg">Cod. Produto</span>
      <input type="number" name="cod_produto" class="form-control" aria-label="Sizing example input" value="{{$produto->cod_produto}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Produto</span>
      <input type="text" name="nome_produto" class="form-control" aria-label="Sizing example input" value="{{$produto->nome_produto}}">
    </div>
    <button class="" type="submit">Editar</button>
</form>

@endsection
