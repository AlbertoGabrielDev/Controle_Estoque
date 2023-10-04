@extends('layouts.principal')

@section('conteudo')

  <h1 class="h1 text-center m-5">Editar Usuario</h1>
  <a class="btn btn-primary m-3" href="{{route('categoria.inicio')}}">Voltar</a>
  
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
    <button class="btn btn-primary m-2" type="submit">Editar</button>
</form>
@endsection
