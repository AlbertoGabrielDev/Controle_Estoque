@extends('layouts.principal')

@section('conteudo')
<div class="categoria">Index Produtos</div> 

<div class="div_criar_produto">
    <a class="button_criar_produto" href="{{route('produtos.cadastro')}}">Cadastrar Produto</a>     
</div>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Produto</th>
        <th scope="col">Quantidade no Estoque(Uni.Med)</th>
        <th scope="col">Validade</th>
        <th scope="col">Categoria</th>
        <th scope="col">Marca</th>
        <th scope="col">Valor</th>
        <th>X</th>
        <th>Y</th>
      </tr>
    </thead>
    <tbody>
      <tr>
       
        <td>Banana</td>
        <td>4kg</td>
        <td>20/12/2023</td>
        <td>Fruta</td>
        <td>Fazendinha</td>
        <td>10,00KG</td>
        <td>Editar</td> 
        <td>Deletar</td>
      </tr>
      <tr>
     
        <td>Alcatra</td>
        <td>50KG</td>
        <td>31/12/2023</td>
        <td>Carne</td>
        <td>Friboi</td>
        <td>31,00KG</td>
        <td>Editar</td> 
        <td>Deletar</td>
      </tr>
      <tr>
        
        <td>Cerveja</td>
        <td>300LT</td>
        <td>05/03/2024</td>
        <td>Bebida</td>
        <td>Skol</td>
        <td>21,00LT</td>
        <td>Editar</td> 
        <td>Deletar</td>
      </tr>
    </tbody>
</table>
        <div>OBS: editar e deletar tem que alterar a tabela estoque/ O estoque so vai servir para vizualizar. 
            Inserir produtos vai ser dentro dessa página/ Todos os dados tem que ser inseridos juntos, assim as chaves primarias serão inseridas juntas e serão iguais </div>
  <br>

 {{--  <div class="categoria">Cadastro de Produtos</div> 

<form>
    <div class="mb-3">
      <label for="exampleInputPassword1" class="form-label mb-4">Produto</label>
      <input type="password" class="form-control form-control-lg w-25" id="exampleInputPassword1">
    </div>
    <div class="div_criar_categoria2">
        <button class="button_criar_categoria2" type="submit">Criar Produto</button>     
    </div>
</form> --}}
@endsection