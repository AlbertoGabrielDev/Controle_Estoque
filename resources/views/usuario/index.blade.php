@extends('layouts.principal')

@section('conteudo')
<div class="usuario">Index Usuario</div> 

<div class="div_criar_produto">
    <a class="button_criar_produto" href="{{route('usuario.cadastro')}}">Cadastrar Usúario</a>     
</div>

<table class="table mt-5">
    <thead>
      <tr>
        <th scope="col">ID Usuario</th>
        <th scope="col">Nome</th>
        <th scope="col">Permissões</th>
        <th>X</th>
        <th>Y</th>
      </tr>
    </thead>
    <tbody>
      <tr>
      <td>1</td>
      <td>Alberto</td>
      <td>Todas</td>
      <td>Editar</td>
      <td>Deletar</td>
      </tr>
    </tbody>
</table>
        <div>OBS: editar e deletar tem que alterar a tabela estoque/ O estoque so vai servir para vizualizar. 
            Inserir produtos vai ser dentro dessa página/ Todos os dados tem que ser inseridos juntos, assim as chaves primarias serão inseridas juntas e serão iguais </div>
  <br>

@endsection