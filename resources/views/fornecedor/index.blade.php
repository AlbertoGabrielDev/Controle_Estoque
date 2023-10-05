@extends('layouts.principal')
@section('conteudo')

  <h1 class="h1 text-center m-5">Index Fornecedores</h1>
  <a class="btn btn-primary m-3" href="{{route('categoria.inicio')}}">Voltar</a>
  <a class="btn btn-primary m-3" href="{{route('fornecedor.cadastro')}}">Cadastrar Fornecedor</a>     

<form action="{{ route('fornecedor.buscar') }}" method="GET" class="d-flex">
  <input type="text" name="nome_fornecedor" class="form-control w-25" placeholder="Digite o nome do Fornecedor">
  <button class="btn btn-outline-success m-1" type="submit">Pesquisar</button>
</form>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">Nome</th>
        <th scope="col">CNPJ</th>
        <th scope="col">CEP</th>
        <th scope="col">Logradouro</th>
        <th scope="col">Bairro</th>
        <th scope="col">N. Casa</th>
        <th scope="col">Email</th>
        <th scope="col">Cidade</th>
        <th scope="col">UF</th>
        <th>Editar</th>
          @can('permissao')
            <th>Inativar</th>
          @endcan
      </tr>
    </thead>
    <tbody>
      @foreach ($fornecedores as $fornecedor)
        <tr>
          <td>{{$fornecedor->nome_fornecedor}}</td>
          <td>{{$fornecedor->cnpj}}</td>
          <td>{{$fornecedor->cep}}</td>
          <td>{{$fornecedor->logradouro}}</td>
          <td>{{$fornecedor->bairro}}</td>
          <td>{{$fornecedor->numero_casa}}</td>
          <td>{{$fornecedor->email}}</td>
          <td>{{$fornecedor->cidade}}</td>
          <td>{{$fornecedor->uf}}</td>
          <td><a href="{{route('fornecedor.editar', $fornecedor->id_fornecedor)}}" class="btn btn-primary">Editar</a></td> 
          <td>
            @can('permissao')
              <button class="btn btn-primary toggle-ativacao  @if($fornecedor->status === 1) btn-danger @elseif($fornecedor->status === 0) btn-success @else btn-primary @endif"" data-id="{{ $fornecedor->id_fornecedor }}">
                {{ $fornecedor->status ? 'Inativar' : 'Ativar' }}
              </button>
            @endcan
          </td> 
        </tr>
      @endforeach
    </tbody>

</table>
<nav class="Page navigation example">
  <ul class="pagination">
    {{ $fornecedores->links()}}
  </ul>
</nav>
@endsection