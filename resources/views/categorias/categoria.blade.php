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

   <div class="div_pai">
        <div class="cards">
            <img src="{{asset('img/Frutas.jpg')}}" class="" alt="...">
            <div class="div_filho">
                <h5 class="titulo">Frutas</h5>
                <p class="descricao">Todas os tipos de frutas frescas aqui. Tem banana, maça, perâ...</p>
                
                <div class="ver_produto">
                    <a class="button_criar_categoria" href="{{route('produtos.inicio')}}">Ver produtos</a>
                </div>

            </div>
        </div>
    </div>
@endsection

