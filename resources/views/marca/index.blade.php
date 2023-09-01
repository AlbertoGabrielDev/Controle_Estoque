@extends('layouts.principal')

@section('conteudo')
<div class="categoria">Index</div> 

<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('marca.cadastro')}}">Cadastrar Marca</a>     
</div>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Marca</th>
        <th scope="col">Id Produto</th>
        <th>X</th>
        <th>Y</th>
      </tr>
    </thead>
    <tbody>
      <tr>
       
        <td>Mark</td>
        <td>Otto</td>
        <td>Editar</td> 
        <td>Inativar</td>
      </tr>
      <tr>
     
        <td>Jacob</td>
        <td>Thornton</td>
        <td>Editar</td> 
        <td>Inativar</td>
      </tr>
      <tr>
        
        <td colspan="2">Larry the Bird</td>
        <td>Editar</td> 
        <td>Inativar</td>
      </tr>
    </tbody>
</table>
  <br>

@endsection