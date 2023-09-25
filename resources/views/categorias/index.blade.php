@extends('layouts.principal')

@section('conteudo')
    <div class="categoria">Index</div> 
    <div class="div_criar_produto">
        <a class="button_criar_produto" href="{{route('categoria.cadastro')}}">Cadastrar Categoria</a>     
    </div>
<table class="table mt-5">
    <thead>
        <tr>
            <th scope="col">Categoria</th>
            <th>X</th>
            <th>Y</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categorias as $categoria)
        <tr>
            <td>{{$categoria->nome_categoria}}</td>
            <td><a href="{{route('categorias.editar', $categoria->id_categoria)}}" class="btn btn-primary">Editar</a></td> 
            <td>
                <button class="btn btn-primary toggle-ativacao @if($categoria->status === 1) btn-danger @elseif($categoria->status === 0) btn-success @else btn-primary @endif" data-id="{{ $categoria->id_categoria }}">
                    {{ $categoria->status ? 'Inativar' : 'Ativar' }}
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
