@extends('layouts.principal')

@section('conteudo')
    <div class="produto">
        Cadastro de Marcas
    </div>

    {{-- {{dd($vars_localidade)}} --}}

    <form action="{{route('marca.inserirMarca')}}" method="POST">
        @csrf
     <div class="estoque_espacamento"></div>
        <div class="row">
            <div class="col-md-4">
              <input type="text" required class="form-control form-control-lg w-75" name="marca" placeholder="Nome da Marca">
            </div>
            <div class="div_criar_marca">
                <button class="button_criar_marca" type="submit">Criar Marca</button>     
            </div>
        </div>
          
    </form>    

@endsection