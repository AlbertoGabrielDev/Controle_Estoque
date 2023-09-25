@extends('layouts.principal')

@section('conteudo')

<div class="container d-flex justify-content-between align-items-center">
    <div class="mx-auto">
      <h1 class="card-title">Cadastro de Fornecedores</h1>
    </div>
    <div>
      <a class="btn btn-primary" href="{{route('fornecedor.index')}}">Voltar</a>
    </div>
</div>


<form action="{{route('fornecedor.inserirCadastro')}}" method="POST" id="cadastro_fornecedor">
    @csrf
 <div class="estoque_espacamento"></div>

    <div class="row">
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="nome_fornecedor" placeholder="Nome da Fornecedor">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75 " required name="cnpj" id="cnpj" placeholder="CNPJ">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75 " required name="cep" id="cep" placeholder="CEP">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="logradouro" id="endereco" placeholder="Logradouro">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="bairro" id="bairro" placeholder="Bairro">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="numero_casa" placeholder="Número">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="email" placeholder="Email">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="ddd" placeholder="DDD">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="cidade" id="cidade" placeholder="Cidade">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="uf" id="uf" placeholder="uf">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required name="telefone" placeholder="Telefone">
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <input type="checkbox" class="btn-check" id="btncheck1" name="principal" value="1" autocomplete="off">
                <label class="btn btn-outline-primary" for="btncheck1">Principal</label>
            </div>
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <input type="checkbox" class="btn-check" id="btncheck2" name="whatsapp" value="1" autocomplete="off">
                <label class="btn btn-outline-primary" for="btncheck2">Whatsapp</label>
            </div>
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <input type="checkbox" class="btn-check" id="btncheck3" name="telegram" value="1" autocomplete="off">
                <label class="btn btn-outline-primary" for="btncheck3">Telegram</label>
            </div>
        <div>
  </div>
    <select name="status" id="status" class="form-control">
        <option value="1">Ativo</option>
        <option value="0">Inativo</option>
    </select>
    </div> 

  <div class="div_criar_fornecedor">
    <button class="button_criar_fornecedor" type="submit">Cadastrar Fornecedor</button>     
  </div>
</form>
@endsection

