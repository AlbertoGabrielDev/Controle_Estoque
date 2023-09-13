@extends('layouts.principal')

@section('conteudo')
    <div class="categoria">Categorias</div> {{--Colocar o titulo de acordo com a rota --}}

    <div class="buscar">
        <input for="search" placeholder="Buscar">
        <button type="submit" class="material-symbols-outlined icon">
        search
        </button>
    </div>
    
    <div class="div_criar_categoria">
        <a class="button_criar_categoria" href="{{route('categoria.index')}}">Criar Categoria</a>
    </div>

    <div class="row">
        @foreach ($categorias as $categoria)
            <div class="col-md-3 mb-3">
                <div class="card">
                    <img src="{{ asset('img/Frutas.jpg') }}" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">{{ $categoria->nome_categoria }}</h5>
                    </div>
                </div>
            </div>
            @if ($loop->iteration % 4 == 0)
                </div><div class="row">
            @endif
        @endforeach
    </div>
@endsection

