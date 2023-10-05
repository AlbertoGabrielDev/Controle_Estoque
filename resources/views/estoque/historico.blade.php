@extends('layouts.principal')
@section('conteudo')


  <h1 class="h1 text-center m-5">Index Historico</h1>
  <a class="btn btn-primary m-3" href="{{route('categoria.inicio')}}">Voltar</a>
 
<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Produto</th>
        <th scope="col">Marca</th>
        <th scope="col">Fornecedor</th>
        <th scope="col">Quantidade retirada</th>
        <th scope="col">Quantidade</th>
        <th scope="col">Data de alteração</th>
        <th scope="col">Usuário que altero</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($historicos as $historico)
      <tr>
        @foreach ($historico->estoques->produtos as $produto)
          <td>{{$produto->nome_produto}}</td>
        @endforeach
        @foreach ($historico->estoques->marcas as $marca)
        <td>{{$marca->nome_marca}}</td>
        @endforeach
        @foreach ($historico->estoques->fornecedores as $fornecedor)
        <td>{{$fornecedor->nome_fornecedor}}</td>
        @endforeach
          <td>{{$historico->quantidade_diminuida}}</td>
          <td>{{$historico->quantidade_historico}}</td>
          <td>{{ \Carbon\Carbon::parse($historico->updated_at)->format('d/m/Y h:i:s A') }}</td> 
          <td>{{$historico->estoques->id_users_fk}}</td>
      </tr>
      @endforeach
    </tbody>
</table>
{{-- <nav class="Page navigation example">
  <ul class="pagination">
    {{ $estoques->links()}}
  </ul>
</nav> --}}
@endsection