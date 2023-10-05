@extends('layouts.principal')

@section('conteudo')
      <h1 class="h1 text-center m-5">{{ $categoria }}</h1>
      <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>

<table class="table mt-5">
  <thead>
    <tr>
      <th scope="col">Cod. Produto</th>
      <th scope="col">Nome Produto</th>
      <th scope="col">Descrição</th>
      <th scope="col">Unidade de Medida</th>
      <th scope="col">Infor. Nutricional</th>
      <th scope="col">Validade</th>
      <th>Editar</th>
      @can('permissao')
        <th>Inativar</th>   
      @endcan
    </tr>
  </thead>
  <tbody>
    @foreach ($produtos as $produto)
        <tr>
          <td>{{$produto->cod_produto}}</td>
          <td>{{$produto->nome_produto}}</td>
          <td>{{$produto->descricao}}</td>
          <td>{{$produto->unidade_medida}}</td>
          <td>
            <button class="btn btn-primary btn-show-nutrition" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBackdrop" aria-controls="offcanvasWithBackdrop" data-produto-id="{{ $produto->id_produto }}">Infor. Nutricionais</button>
            <span class="nutritional-info" data-produto-id="{{ $produto->id_produto }}" style="display: none;">{{json_decode($produto->inf_nutrientes)}}</span>
          </td>
          <td class= "expiration-date" id="data">{{($produto->validade) }}</td>
          <td><a href="{{route('produtos.editar', $produto->id_produto)}}" class="btn btn-primary">Editar</a></td>
          <td>
            @can('permissao')
              <button class="btn btn-primary toggle-ativacao @if($produto->status === 1) btn-danger @elseif($produto->status === 0) btn-success @else btn-primary @endif" data-id="{{ $produto->id_produto}}">
                {{ $produto->status ? 'Inativar' : 'Ativar' }}
              </button>
            @endcan
          </td>
        </tr>
      @endforeach
  </tbody>
</table>
<script>
  $(document).ready(function () {
    var grupo = window.location.pathname.split('/')[4];
    console.log(grupo);
  $('.toggle-ativacao').click(function () 
  {
    var button = $(this);
    var produtoId = button.data('id');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
      url: '/verdurao/categoria/produto/status/'+ produtoId,
      method: 'POST',
      headers: 
      {
        'X-CSRF-TOKEN': csrfToken
      },
      success: function (response) 
      {
        if (response.status === 1) {
          button.text('Inativar').removeClass('btn-success').addClass('btn-danger');
        } else 
        {
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