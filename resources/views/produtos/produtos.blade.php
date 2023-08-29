@extends('layouts.principal')

@section('conteudo')
    <div class="produto">Produtos</div> {{--Colocar o titulo de acordo com a rota --}}

    <div class="buscar">
        <input for="search" placeholder="Buscar">
        <button type="submit" class="material-symbols-outlined icon">
        search
        </button>
    </div>

    <div class="div_criar_produto">
        <a class="button_criar_produto" href="{{route('produtos.cadastro')}}">Cadastrar Produto</a>     
    </div>

    <div class="div_pai">
        <div class="cards">
            <img src="{{asset('img/banana.png')}}" class="" alt="...">
            <div class="div_filho">
                <h5 class="titulo">Banana</h5>
                <p class="descricao">Banana......</p>
                
                <div class="div_inf_nutri">
                    <div class="valor">
                        Preço: 10,00
                    </div>
                    <div class="inf_nutri">Inf. Nutricional</div>     
                </div>
            </div>
        </div>
    </div>
@endsection