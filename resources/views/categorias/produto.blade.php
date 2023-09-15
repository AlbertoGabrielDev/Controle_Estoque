@extends('layouts.principal')

@section('conteudo')

<div class="container d-flex justify-content-between align-items-center">
    <div class="mx-auto">
      <h1 class="card-title">{{ $variaveis->first()->nome_categoria }}</h1>
    </div>
    <div>
      <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>
    </div>
  </div>
</div>
    <table class="table">
        <thead>
            <tr>
              <th scope="col">Cod. Produto</th>
              <th scope="col">Nome Produto</th>
              <th scope="col">Descrição</th>
              <th scope="col">Unidade de Medida</th>
              <th scope="col">Infor. Nutricional</th>
              <th scope="col">Validade</th>
              <th>Editar</th>
              <th>Inativar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($variaveis as $variavel)
            <tr>
                <td>{{$variavel->cod_produto}}</td>
                <td>{{$variavel->nome_produto}}</td>
                <td>{{$variavel->descricao}}</td>
                <td>{{$variavel->unidade_medida}}</td>
                <td>{{$variavel->inf_nutrientes}}</td>
                <td>{{ \Carbon\Carbon::parse($variavel->validade)->format('d/m/Y') }}</td> 
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection