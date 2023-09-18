@extends('layouts.principal')

@section('conteudo')
<div class="container d-flex justify-content-between align-items-center">
  <div class="mx-auto">
    <h1 class="card-title">Index Produtos</h1>
  </div>
  <div>
    <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>
  </div>
</div>

<div class="div_criar_produto">
  <a class="button_criar_produto" href="{{route('produtos.cadastro')}}">Cadastrar Produto</a>     
</div>


<form action="{{ route('produtos.buscar') }}" method="GET" class="d-flex">
  <input type="text" name="nome_produto" class="form-control w-25" placeholder="Procurar">
  <button class="btn btn-outline-success" type="submit">Pesquisar</button>
</form>

<table class="table mt-5">
    <thead>
      <tr>
        {{-- <th>Categoria</th> --}}
        <th scope="col">Cod. Produto</th>
        <th scope="col">Nome Produto</th>
        <th scope="col">Descrição</th>
        <th scope="col">Unidade de Medida</th>
        <th scope="col">Infor. Nutricional</th>
        <th scope="col">Validade</th>
        <th>Editar</th>
        <th>Inativar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($produtos as $produto)
          <tr>
            <td>{{$produto->cod_produto}}</td>
            <td>{{$produto->nome_produto}}</td>
            <td>{{$produto->descricao}}</td>
            <td>{{$produto->unidade_medida}}</td>
            <td>{{$produto->inf_nutrientes}}</td>
            <td>{{ \Carbon\Carbon::parse($produto->validade)->format('d/m/Y') }}</td> 
            <td> <a href="{{route('produtos.editar', $produto->id_produto)}}" class="btn btn-primary">Editar</a></td>
            <td>
              <button class="btn btn-primary delete-button" data-id={{$produto->id_produto}}>Excluir</button>
            </td>
     
          </tr>
        @endforeach
      
    </tbody>
</table>

<script>
  $(document).ready(function() {
      $('.delete-button').click(function() {
          const produtoId = $(this).data('id');

          if (confirm('Tem certeza de que deseja excluir este produto?')) {
              $.ajax({
                  url: '/verdurao/produtos/delete/' + produtoId,
                  type: 'DELETE',
                  data: {
                      _token: '{{ csrf_token() }}'
                  },
                  success: function(response) {
                      location.reload();
                  },
                  error: function() {
                      alert('Erro ao excluir o produto.');
                  }
              });
          }
      });
  });
</script>
@endsection