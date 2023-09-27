@extends('layouts.principal')
@section('conteudo')

<div class="container d-flex justify-content-between align-items-center">
  <div class="mx-auto">
    <h1 class="card-title">Index Historico</h1>
  </div>
  <div>
    <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>
  </div>
</div>
<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Id Produto</th>
        <th scope="col">Id Estoque</th>
        <th scope="col">Quantidade retirada</th>
        <th scope="col">Quantidade</th>
        <th scope="col">Data de alteração</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($estoques as $estoque)
      <tr>
          <td>{{$estoque->id_produto_fk}}</td>
          <td>{{$estoque->id_estoque}}</td>
          <td></td>
          <td>{{$estoque->quantidade}}</td>
          <td>{{ \Carbon\Carbon::parse($estoque->updated_at)->format('d/m/Y') }}</td> 
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