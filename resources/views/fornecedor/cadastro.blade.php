@extends('layouts.principal')

@section('conteudo')

    <h1 class="h1 text-center m-5">Cadastro de Fornecedores</h1>
    <a class="btn btn-primary m-3" href="{{route('fornecedor.index')}}">Voltar</a>

<form action="{{route('fornecedor.inserirCadastro')}}" method="POST" id="cadastro_fornecedor">
    @csrf
 <div class="estoque_espacamento"></div>

    <div class="row">
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required value="{{old('nome_fornecedor')}}" name="nome_fornecedor" placeholder="Nome da Fornecedor">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75 " required value="{{old('cnpj')}}" name="cnpj" id="cnpj" placeholder="CNPJ">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75 " required value="{{old('cep')}}" name="cep" id="cep" placeholder="CEP">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required value="{{old('logradouro')}}" name="logradouro" id="endereco" placeholder="Logradouro">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required value="{{old('bairro')}}" name="bairro" id="bairro" placeholder="Bairro">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required value="{{old('numero_casa')}}" name="numero_casa" placeholder="Número">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required value="{{old('email')}}" name="email" placeholder="Email">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required value="{{old('ddd')}}" name="ddd" placeholder="DDD">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required value="{{old('cidade')}}" name="cidade" id="cidade" placeholder="Cidade">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required value="{{old('uf')}}" name="uf" id="uf" placeholder="uf">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-lg w-75" required value="{{old('telefone')}}" name="telefone" placeholder="Telefone">
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
    <button class="btn btn-primary m-1" type="submit">Cadastrar Fornecedor</button>     
</form>
@endsection

