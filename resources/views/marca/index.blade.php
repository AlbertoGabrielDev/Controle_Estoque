@extends('layouts.principal')

@section('conteudo')
<div class="container d-flex justify-content-between align-items-center">
  <div class="mx-auto">
    <h1 class="card-title">Index Marca</h1>
  </div>
  <div>
    <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>
  </div>
</div>
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
          <button class="btn btn-primary toggle-ativacao" data-id="{{ $marca->id_marca }}" data-status="{{ $marca->status ? 'true' : 'false' }}">
            {{ $marca->status ? 'Inativar' : 'Ativar' }}
          </button>
        </td>
      </tr>
      @endforeach
    </tbody>
</table>
<script>
  $(document).ready(function () {
      $('.toggle-ativacao').click(function () {
          var button = $(this);
          var produtoId = button.data('id');
          console.log(button);
          var csrfToken = $('meta[name="csrf-token"]').attr('content');

          $.ajax({
              url: '/verdurao/marca/status/' + produtoId,
              method: 'POST',
              headers: {
                  'X-CSRF-TOKEN': csrfToken
              },
              success: function (data) {
                  if (data.status === 1) {
                      button.text('Inativar');
                      button.data('status', true);
                  } else {
                      button.text('Ativar');
                      button.data('status', false);
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