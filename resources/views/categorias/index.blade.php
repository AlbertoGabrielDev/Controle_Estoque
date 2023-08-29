@extends('layouts.principal')

@section('conteudo')
    <div class="categoria">Index</div> 

    <div class="">
        <button class="button_criar_categoria" type="submit"></button>     
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
      <br>

      <div class="categoria">Cadastro de Categoria</div> 

    <form>
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label mb-4">Categoria</label>
          <input type="password" class="form-control form-control-lg w-25" id="exampleInputPassword1">
        </div>
        <div class="div_criar_categoria2">
            <button class="button_criar_categoria2" type="submit">Criar Categoria</button>     
        </div>
    </form>
@endsection
