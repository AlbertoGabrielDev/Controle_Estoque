@extends('layouts.principal')


@section('conteudo')
    <div class="h1 text-center m-5">Editar Marca</div>

<form action="{{route('marca.editar' , $marcas->first()->id_marca)}}" method="POST">
    @csrf
    <div class="estoque_espacamento"></div>
    <div class="row">
        @foreach ($marcas as $marca)
        <div class="col-md-4">
            <input type="text" required class="form-control form-control-lg w-75" name="nome_marca" value="{{$marca->nome_marca}}" placeholder="Nome da Marca">
        </div>
        @endforeach
    </div>   
    <button class="btn btn-primary m-1" type="submit">Editar Marca</button>   
</form>    

@endsection