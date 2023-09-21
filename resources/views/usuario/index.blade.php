@extends('layouts.principal')

@section('conteudo')
<div class="usuario">Index Usuario</div> 

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
          <button class="btn btn-primary toggle-ativacao" data-id="{{ $usuario->id}}" data-status="{{ $usuario->status ? 'true' : 'false' }}">
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