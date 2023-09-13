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
            <td>Editar</td> 
            <td>Inativar</td>
          </tr>
          @endforeach
        </tbody>
    </table>
      <br>
@endsection
