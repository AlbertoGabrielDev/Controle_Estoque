@extends('layouts.principal')

@section('conteudo')
<div class="produto">
    Editar Categoria
</div>
<form action="{{route('categorias.editar' , $categorias->first()->id_categoria)}}" method="POST">
    @csrf
    <div class="estoque_espacamento"></div>
    <div class="row">
        @foreach ($categorias as $categoria)
        <div class="col-md-4">
            <input type="text" required class="form-control form-control-lg w-75" name="nome_categoria" value="{{$categoria->nome_categoria}}" placeholder="Nome da Marca">
        </div>
        @endforeach
        <div class="div_criar_marca">
            <button class="button_criar_marca" type="submit">Editar Marca</button>     
        </div>
    </div>   
</form>    
@endsection