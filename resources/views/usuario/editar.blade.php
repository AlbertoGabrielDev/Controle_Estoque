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
<form action="{{route('usuario.salvarEditar', $usuarios->first()->id)}}" method="POST">
  @csrf
  @foreach ($usuarios as $usuario)
    <div class="input-group input-group-lg">
      <span class="input-group-text" id="inputGroup-sizing-lg">Cod. Usuario</span>
      <input type="number" name="id" class="form-control" aria-label="Sizing example input" disabled value="{{$usuario->id}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Nome Usuario</span>
      <input type="text" name="name" class="form-control" aria-label="Sizing example input" value="{{$usuario->name}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Email</span>
      <input type="text" name="email" class="form-control" aria-label="Sizing example input" value="{{$usuario->email}}">
    </div>
    @endforeach
    <button class="" type="submit">Editar</button>
</form>
@endsection
