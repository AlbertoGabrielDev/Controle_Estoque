@extends('layouts.principal')

@section('conteudo')
<div class="usuario">Index Usuario</div> 

<form action="{{ route('usuario.buscar') }}" class="d-flex" method="GET">
  <input type="text" name="name" class="form-control w-25" placeholder="Digite o nome da Marca">
  <button class="btn btn-outline-success" type="submit">Pesquisar</button>
</form>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">ID Usuario</th>
        <th scope="col">Nome</th>
        <th scope="col">Email</th>
        <th>Editar</th>
        <th>Ativar/Inativar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($usuarios as $usuario)
      <tr>
        <td>{{$usuario->id}}</td>
        <td>{{$usuario->name}}</td>
        <td>{{$usuario->email}}</td>
        <td>Editar</td>
        <td>
          <button class="btn btn-primary toggle-ativacao @if($usuario->status === 1) btn-danger @elseif($usuario->status === 0) btn-success @else btn-primary @endif" data-id="{{ $usuario->id}}">
            {{ $usuario->status ? 'Inativar' : 'Ativar' }}
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
        url: '/verdurao/usuario/status/' + produtoId,
        method: 'POST',
        headers: {
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