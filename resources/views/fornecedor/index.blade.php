@extends('layouts.principal')

@section('conteudo')

<div class="fornecedor">Index Fornecedores</div> 

<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('fornecedor.cadastro')}}">Cadastrar Fornecedor</a>     
</div>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Nome</th>
        <th scope="col">Cod. Fornecedor</th>
        <th scope="col">Cod.Produto</th>
        <th scope="col">Categoria do Produto</th>
        <th scope="col">Nome Produto</th>
        <th scope="col">Preço do Fornecedor</th>
        <th>X</th>
        <th>Y</th>
      </tr>
    </thead>
    <tbody>
      <tr>
    
        <td>Friboi</td>
        <td>5</td>
        <td>25</td>
        <td>Carne</td>
        <td>Alcatra</td>
        <td>15,00KG</td>
        <td>Editar</td> 
        <td>Deletar</td>
      </tr>
      <tr>
     
        <td>Fazendinha</td>
        <td>3</td>
        <td>32</td>
        <td>Fruta</td>
        <td>Banana</td>
        <td>6,00KG</td> 
        <td>Editar</td>
        <td>Deletar</td>
      </tr>
      <tr>
        
        <td>Skol</td>
        <td>39</td>
        <td>65</td>
        <td>Bebida</td>
        <td>Skol</td>
        <td>8,00KG</td>
        <td>Editar</td> 
        <td>Deletar</td>
      </tr>
    </tbody>
</table>
@endsection