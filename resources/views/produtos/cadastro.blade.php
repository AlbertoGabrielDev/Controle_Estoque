@extends('layouts.principal')

@section('conteudo')
    <div class="produto">
        Cadastro de Produtos
    </div>

    <form action="">
     <div class="estoque_espacamento"></div>
        <div class="row">
            <div class="col-md-4">
              <input type="text" class="form-control form-control-lg w-75" placeholder="Nome do Produto">
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control form-control-lg w-75" placeholder="Descrição do produto">
            </div>
            <div class="col-md-4">
              <input type="Date" class="form-control form-control-lg w-75" placeholder="Validade do produto">
            </div>
        </div>
          
        <div class="row">
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" placeholder="Lote">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" placeholder="Unidade de Medida">
            </div>
            <div class="col-md-4">
                <input type="Number" class="form-control form-control-lg w-75" placeholder="Preço">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" placeholder="Marca">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" placeholder="Quantidade">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" placeholder="Localização no Estoque">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="Date" class="form-control form-control-lg w-75" placeholder="Data de Entrega">
            </div>
            <div class="col-md-4">
                <input type="Date" class="form-control form-control-lg w-75" placeholder="Data de Cadastro">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control form-control-lg w-75" placeholder="Nome Fornecedor">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" placeholder="Preço Fornecedor">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" placeholder="Valor Energético">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" placeholder="Carboidrato">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" placeholder="Proteinas">
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control form-control-lg w-75" placeholder="Sódio">
            </div>
        </div>
        <div class="div_criar_categoria2">
            <button class="button_criar_categoria2" type="submit">Criar Produto</button>     
        </div>
      
              
       
        
    </form>    

@endsection