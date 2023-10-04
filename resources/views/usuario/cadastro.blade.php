@extends('layouts.principal')

@section('conteudo')
<div class="h1 text-center m-5">Inserir Usuário</div> 

<form action="{{route('usuario.inserirUsuario')}}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" name="nome_usuario" placeholder="Nome do Usuário">
        </div>
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" name="login"  placeholder="Login">
        </div>
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" name="senha"  placeholder="Senha">
        </div>
    </div>
    </div>
    <button class="btn btn-primary m-2" type="submit">Criar Produto</button>          
</form>    
@endsection