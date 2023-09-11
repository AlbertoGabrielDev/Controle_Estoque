@extends('layouts.principal')

@section('conteudo')
<div class="categoria">Index</div> 

<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('marca.cadastro')}}">Cadastrar Marca</a>     
</div>

<form action="{{ route('marca.buscar') }}" method="GET">
  <input type="text" name="nome_marca" placeholder="Digite o nome da Marca">
  <button type="submit">Pesquisar</button>
</form>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Marca</th>
        <th>X</th>
        <th>Y</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($marcas as $marca)
      <tr>
          <td>{{$marca->nome_marca}}</td>
          {{-- <td><a href="{{route('marca.editar', ['id'=> $editar->id_marca])}}"></a>Editar</td>  --}}
          <td>Inativar</td>
      </tr>
      @endforeach
    </tbody>
</table>
  <br>

@endsection