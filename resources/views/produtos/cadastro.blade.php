@extends('layouts.principal')

@section('conteudo')
    <div class="container d-flex justify-content-between align-items-center">
        <div class="mx-auto">
          <h1 class="card-title">Cadastro de Produtos</h1>
        </div>
        <div>
          <a class="btn btn-primary" href="{{route('produtos.index')}}">Voltar</a>
        </div>
    </div>
    <form action="{{route('produtos.salvarCadastro')}}" method="POST">
        @csrf
     <div class="estoque_espacamento"></div>
        <div class="row">
            <div class="col-md-4">
              <input type="text" class="form-control form-control-lg w-75" value="{{old('nome_produto')}}" name="nome_produto" placeholder="Nome do Produto">
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control form-control-lg w-75" value="{{old('descricao')}}" name="descricao"  placeholder="Descrição do produto">
            </div>
            <div class="col-md-4">
              <input type="Date" class="form-control form-control-lg w-75" value="{{old('validade')}}" name="validade"  placeholder="Validade do produto">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" value="{{old('unidade_medida')}}" name="unidade_medida"  placeholder="Unidade de Medida">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" value="{{old('cod_produto')}}" name="cod_produto"  placeholder="Cod. Produto">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" value="{{old('inf_nutrientes')}}" name="inf_nutrientes"  placeholder="Inf. Nutricionais">
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-control form-control-lg w-75" name="nome_categoria" >
                <option value="{{old('nome_categoria')}}">Selecione uma Categoria</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id_categoria }}">{{ $categoria->nome_categoria}}</option>
                @endforeach
            </select>
       </div>
        <div class="div_criar_categoria2">
            <button class="button_criar_categoria2" type="submit">Criar Produto</button>     
        </div>        
    </form>    
@endsection