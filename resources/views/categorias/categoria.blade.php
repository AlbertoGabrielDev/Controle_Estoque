@extends('layouts.principal')

@section('conteudo')
    <div class="categoria">Categorias</div> {{--Colocar o titulo de acordo com a rota --}}

    <div class="buscar">
        <input for="search" placeholder="Buscar">
        <button type="submit" class="material-symbols-outlined icon">
        search
        </button>
    </div>
    
    <div class="criar_categoria">
        <button class="button_criar_categoria" type="submit">Criar Categoria</button>     
    </div>

   <div class="div_pai">
        <div class="cards">
            <img src="{{asset('img/Frutas.jpg')}}" class="" alt="...">
            <div class="div_filho">
                <h5 class="titulo">Frutas</h5>
                <p class="descricao">Todas os tipos de frutas frescas aqui. Tem banana, maça, perâ...</p>
                
                <div class="ver_produto">
                    <button class="button_criar_categoria" type="submit" class="">Ver produtos</button>
                </div>

            </div>
        </div>
    </div>
@endsection

