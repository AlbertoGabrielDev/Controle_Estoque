@extends('layouts.principal')

@section('conteudo')
<div class="h1 text-center m-5">Categorias</div> {{--Colocar o titulo de acordo com a rota --}}

    <a class="btn btn-primary m-1" href="{{route('categoria.index')}}">Index</a>

    <div class="row">
        @foreach ($categorias as $categoria)
            <div class="col-md-3">
                <div class="card">
                    <img src="/img/categorias/{{$categoria->imagem}}" class="card-img-top " alt="{{$categoria->nome_categoria}}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $categoria->nome_categoria }}</h5>
                        @if ($categoria->produtos->count() > 0)
                            <a href="{{ route('categorias.produto', $categoria->id_categoria) }}" class="btn btn-primary">Ver Produtos</a>
                        @else
                            <p>Nenhum produto disponível nesta categoria.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
