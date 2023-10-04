@extends('layouts.principal')

@section('conteudo')

  <h1 class="h1 text-center m-5">Cadastro de Marcas</h1>
  <a class="btn btn-primary m-3" href="{{route('marca.index')}}">Voltar</a>

<form action="{{route('marca.inserirMarca')}}" method="POST">
  @csrf
  <div class="row">
      <div class="col-md-4">
        <input type="text" required class="form-control form-control-lg w-75" name="nome_marca" placeholder="Nome da Marca">
      </div> 
    </div>       
  <button class="btn btn-primary m-1" type="submit">Criar Marca</button>     
</form>    
@endsection