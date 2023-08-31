@extends('layouts.principal')

@section('conteudo')
<div class="categoria">Inserir Usuário</div> 

<form action="{{route('usuario.inserirUsuario')}}" method="POST">
    @csrf
 <div class="estoque_espacamento"></div>
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
    <div class="div_criar_categoria2">
        <button class="button_criar_categoria2" type="submit">Criar Produto</button>     
    </div>
          
</form>    
@endsection