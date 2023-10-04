@extends('layouts.principal')

@section('conteudo')

  <h1 class="h1 text-center m-5">Index Marca</h1>
  <a class="btn btn-primary m-3" href="{{route('categoria.inicio')}}">Voltar</a>
  <a class="btn btn-primary m-3" href="{{route('marca.cadastro')}}">Cadastrar Marca</a>     

<form action="{{ route('marca.buscar') }}" class="d-flex" method="GET">
  <input type="text" name="nome_marca" class="form-control w-25" placeholder="Digite o nome da Marca">
  <button class="btn btn-outline-success m-1" type="submit">Pesquisar</button>
</form>
<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Marca</th>
        <th>Editar</th>
        <th>Inativar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($marcas as $marca)
      <tr>
        <td>{{$marca->nome_marca}}</td>
        <td><a href="{{route('marca.editar', $marca->id_marca)}}" class="btn btn-primary">Editar</a></td> 
        <td>
          <button class="btn btn-primary toggle-ativacao  @if($marca->status === 1) btn-danger @elseif($marca->status === 0) btn-success @else btn-primary @endif"" data-id="{{ $marca->id_marca }}" >
            {{ $marca->status ? 'Inativar' : 'Ativar' }}
          </button>
        </td>
      </tr>
      @endforeach
    </tbody>
</table>
<nav class="Page navigation example">
  <ul class="pagination">
    {{ $marcas->links()}}
  </ul>
</nav>
@endsection