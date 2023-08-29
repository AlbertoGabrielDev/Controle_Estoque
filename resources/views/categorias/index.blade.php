@extends('layouts.principal')

@section('conteudo')
    <div class="categoria">Index</div> 

    <div class="criar_categoria">
        <button class="button_criar_categoria" type="submit">Criar Categoria</button>     
    </div>

    <table class="table mt-5">
        <thead>
          <tr>
            <th scope="col">Categoria</th>
            <th scope="col">Quantidade</th>
            <th>X</th>
            <th>Y</th>
          </tr>
        </thead>
        <tbody>
          <tr>
           
            <td>Mark</td>
            <td>Otto</td>
            <td>Editar</td> 
            <td>Inativar</td>
          </tr>
          <tr>
         
            <td>Jacob</td>
            <td>Thornton</td>
            <td>Editar</td> 
            <td>Inativar</td>
          </tr>
          <tr>
            
            <td colspan="2">Larry the Bird</td>
            <td>Editar</td> 
            <td>Inativar</td>
          </tr>
        </tbody>
      </table>
@endsection
