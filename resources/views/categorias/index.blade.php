@extends('layouts.principal')

@section('conteudo')
    <div class="categoria">Index</div> 
    <div class="div_criar_produto">
        <a class="button_criar_produto" href="{{route('categoria.cadastro')}}">Cadastrar Categoria</a>     
    </div>
<table class="table mt-5">
    <thead>
        <tr>
            <th scope="col">Categoria</th>
            <th>X</th>
            <th>Y</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categorias as $categoria)
        <tr>
            <td>{{$categoria->nome_categoria}}</td>
            <td><a href="{{route('categorias.editar', $categoria->id_categoria)}}" class="btn btn-primary">Editar</a></td> 
            <td>
                <button class="btn btn-primary toggle-ativacao @if($categoria->status === 1) btn-danger @elseif($categoria->status === 0) btn-success @else btn-primary @endif" data-id="{{ $categoria->id_categoria }}">
                    {{ $categoria->status ? 'Inativar' : 'Ativar' }}
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
        url: '/verdurao/categoria/status/' + produtoId,
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
