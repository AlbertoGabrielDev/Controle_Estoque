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
            <input type="text" required class="form-control form-control-lg w-75" name="nome" value="{{$categoria->nome}}" placeholder="Nome da Marca">
        </div>
        @endforeach
    </div>   
    <button class="btn btn-primary m-1" type="submit">Editar Marca</button>     
</form>    
@endsection