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
        <th>X</th>
        <th>Y</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        @foreach ($marca as $marcas)
            <td>{{$marcas->nome_marca}}</td>
        @endforeach
        <td>Editar</td> 
        <td>Inativar</td>
      </tr>
      <tr>
    
    </tbody>
</table>
  <br>

@endsection