@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
    <div class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center ">Cadastro de Produtos</div> 
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('produtos.index')}}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
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
                <input type="text" class="form-control form-control-lg w-75" value="{{old('inf_nutrientes')}}" name="inf_nutrientes"  placeholder="Inf. Nutricionais">
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
            <select class="form-control form-control-lg w-75" name="nome_categoria" >
                <option value="{{old('nome_categoria')}}">Selecione uma Categoria</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id_categoria }}">{{ $categoria->nome_categoria}}</option>
                @endforeach
            </select>
            </div>
        </div>
            <button class="btn btn-primary m-2" type="submit">Criar Produto</button>          
    </form>    
</div>
@endsection