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
      @foreach ($marca as $marcas)
      <tr>
          <td>{{$marcas->nome_marca}}</td>
          <td>Editar</td> 
          <td>Inativar</td>
      </tr>
      @endforeach
    </tbody>
</table>
  <br>

@endsection