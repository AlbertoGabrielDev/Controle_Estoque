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
                    {{-- <div class="inf_nutri">Quantidade</div>   --}}
                    <div class="stepper inf_nutri">
                        <button class="stepper-btn decrement">-</button>
                        <input class="stepper-input" type="number" value="1" min="1" max="10">
                        <button class="stepper-btn increment">+</button>
                      </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const decrementBtn = document.querySelector('.decrement');
        const incrementBtn = document.querySelector('.increment');
        const stepperInput = document.querySelector('.stepper-input');
      
        decrementBtn.addEventListener('click', () => {
          if (stepperInput.value > 1) {
            stepperInput.value = parseInt(stepperInput.value) - 1;
          }
        });
      
        incrementBtn.addEventListener('click', () => {
          if (stepperInput.value < 30) {
            stepperInput.value = parseInt(stepperInput.value) + 1;
          }
        });
      </script>
@endsection

