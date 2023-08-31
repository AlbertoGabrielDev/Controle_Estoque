@extends('layouts.principal')

@section('conteudo')
    <div class="produto">
        Cadastro de Produtos
    </div>

    {{-- {{dd($vars_localidade)}} --}}

    <form action="{{route('produtos.salvarCadastro')}}" method="POST">
        @csrf
     <div class="estoque_espacamento"></div>
        <div class="row">
            <div class="col-md-4">
              <input type="text" class="form-control form-control-lg w-75" name="nome_produto" placeholder="Nome do Produto">
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control form-control-lg w-75" name="descricao"  placeholder="Descrição do produto">
            </div>
            <div class="col-md-4">
              <input type="Date" class="form-control form-control-lg w-75" name="validade"  placeholder="Validade do produto">
            </div>
        </div>
          
        <div class="row">
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" name="lote"  placeholder="Lote">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" name="unidade_medida"  placeholder="Unidade de Medida">
            </div>
            <div class="col-md-4">
                <input type="Number" class="form-control form-control-lg w-75" name="preco_produto" placeholder="Preço">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" name="marca"  placeholder="Marca">
            </div>
            {{-- <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" name="categoria" placeholder="Categoria">
            </div> --}}
           <div class="col-md-4">
                <select class="form-control form-control-lg w-75" name="categoria" required>
                    <option value="">Selecione uma Categoria</option>
                    @foreach ($dados as $categorias)
                        <option value="{{ $categorias->id_categoria }}">{{ $categorias->categoria }}</option>
                    @endforeach
                </select>
           </div>
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" name="localizacao"  placeholder="Localização no Estoque">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="Date" class="form-control form-control-lg w-75" name="data_entrega"  placeholder="Data de Entrega">
            </div>
            <div class="col-md-4">
                <input type="Date" class="form-control form-control-lg w-75" name="data_cadastro"  placeholder="Data de Cadastro">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" name="nome_fornecedor"  placeholder="Nome Fornecedor">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" name="preco_fornecedor"  placeholder="Preço Fornecedor">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" name="quantidade"  placeholder="Quantidade">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" name="carboidrato"  placeholder="Carboidrato">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" name="proteina" placeholder="Proteinas">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" name="sodio" placeholder="Sódio">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" name="valor_energetico"  placeholder="Valor Energético">
            </div>
        </div>
        <div class="div_criar_categoria2">
            <button class="button_criar_categoria2" type="submit">Criar Produto</button>     
        </div>
              
    </form>    

@endsection