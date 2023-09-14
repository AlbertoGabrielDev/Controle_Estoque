@extends('layouts.principal')

@section('conteudo')
<div class="container d-flex justify-content-between align-items-center">
    <div class="mx-auto">
      <h1 class="card-title">Cadastro de Marcas</h1>
    </div>
    <div>
      <a class="btn btn-primary" href="{{route('marca.index')}}">Voltar</a>
    </div>
</div>

    <form action="{{route('marca.inserirMarca')}}" method="POST">
        @csrf
     <div class="estoque_espacamento"></div>
        <div class="row">
            <div class="col-md-4">
              <input type="text" required class="form-control form-control-lg w-75" name="nome_marca" placeholder="Nome da Marca">
            </div>
            <div class="div_criar_marca">
                <button class="button_criar_marca" type="submit">Criar Marca</button>     
            </div>
        </div>
          
    </form>    

@endsection