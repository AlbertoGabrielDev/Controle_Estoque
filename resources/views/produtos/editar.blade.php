@extends('layouts.principal')

@section('conteudo')

  <h1 class="h1 text-center m-5">Editar Produtos</h1>
  <a class="btn btn-primary m-2" href="{{route('categoria.inicio')}}">Voltar</a>

<form action="{{route('produtos.salvarEditar', $produtos->first()->id_produto)}}" method="POST">
  @csrf
  @foreach ($produtos as $produto)
    <div class="input-group input-group-lg">
      <span class="input-group-text" id="inputGroup-sizing-lg">Cod. Produto</span>
      <input type="number" name="cod_produto" class="form-control" disabled aria-label="Sizing example input" value="{{$produto->cod_produto}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Produto</span>
      <input type="text" name="nome_produto" class="form-control" disabled aria-label="Sizing example input" value="{{$produto->nome_produto}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Descrição</span>
      <input type="text" name="descricao" class="form-control" aria-label="Sizing example input" value="{{$produto->descricao}}">
    </div>
    <div class="input-group input-group-lg">
      <span class="input-group-text" id="inputGroup-sizing-lg">Inf. Nutricionais</span>
      <input type="text" name="inf_nutrientes" class="form-control" aria-label="Sizing example input" value="{{json_decode($produto->inf_nutrientes)}}">
    </div>
 @endforeach
    <button class="btn btn-primary m-2" type="submit">Editar</button>
</form>
@endsection
