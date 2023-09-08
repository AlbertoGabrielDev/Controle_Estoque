@extends('layouts.principal')
@section('conteudo')

<div class="estoque">Index Estoque</div> 

<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('estoque.cadastro')}}">Cadastrar Estoque</a>     
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
@endsection