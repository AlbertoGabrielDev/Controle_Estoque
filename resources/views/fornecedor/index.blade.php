@extends('layouts.principal')
@section('conteudo')
<div class="container d-flex justify-content-between align-items-center">
  <div class="mx-auto">
    <h1 class="card-title">Index Fornecedores</h1>
  </div>
  <div>
    <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>
  </div>
</div>

<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('fornecedor.cadastro')}}">Cadastrar Fornecedor</a>     
</div>

<form action="{{ route('fornecedor.buscar') }}" method="GET" class="d-flex">
  <input type="text" name="nome_fornecedor" class="form-control w-25" placeholder="Digite o nome do Fornecedor">
  <button class="btn btn-outline-success" type="submit">Pesquisar</button>
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
        <th>Inativar</th>
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
            <button class="btn btn-primary toggle-ativacao  @if($fornecedor->status === 1) btn-danger @elseif($fornecedor->status === 0) btn-success @else btn-primary @endif"" data-id="{{ $fornecedor->id_fornecedor }}">
              {{ $fornecedor->status ? 'Inativar' : 'Ativar' }}
            </button>
          </td> 
        </tr>
      @endforeach
    </tbody>

</table>
<div class="d-flex justify-content-center">
  {{ $fornecedores->links()}}
</div>
<script>
$(document).ready(function () 
{
  $('.toggle-ativacao').click(function () 
{
  var button = $(this);
  var produtoId = button.data('id');
  var csrfToken = $('meta[name="csrf-token"]').attr('content');
  $.ajax({
    url: '/verdurao/fornecedor/status/' + produtoId,
    method: 'POST',
    headers: 
    {
      'X-CSRF-TOKEN': csrfToken
    },
    success: function (response) 
    {
      if (response.status === 1) 
      {
        button.text('Inativar').removeClass('btn-success').addClass('btn-danger');
      } else {
        button.text('Ativar').removeClass('btn-danger').addClass('btn-success');
      }
    },
    error: function () {
        console.log(error);
    }
  });
});
});
</script> 
@endsection