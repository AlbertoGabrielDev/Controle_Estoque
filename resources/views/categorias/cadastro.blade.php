@extends('layouts.principal')

@section('conteudo')
<div class="h1 text-center m-5">Cadastro de Categoria</div> 

<form action="{{route('categoria.inserirCategoria')}}" method="POST" enctype="multipart/form-data">
  @csrf
    <div class="mb-3">
      <label for="categoria" class="form-label mb-4">Categoria</label>
      <input type="text" name="categoria" required class="form-control form-control-lg w-25" id="exampleInputPassword1">
    </div>
    <div class="mb-5">
      <label for="imagem" class="form-label mb-4">Imagem</label>
      <input type="file" name="imagem" required class="form-control form-control-lg w-25" id="exampleInputPassword1">
    </div>

    <button class="btn btn-primary" type="submit">Criar Categoria</button>     
</form>
@endsection