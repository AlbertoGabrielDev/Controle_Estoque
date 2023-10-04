@extends('layouts.principal')

@section('conteudo')

  <h1 class="h1 text-center m-5">Editar Fornecedor</h1>
  <a class="btn btn-primary m-3" href="{{route('categoria.inicio')}}">Voltar</a>

<form action="{{route('fornecedor.salvarEditar', $fornecedores->first()->id_fornecedor)}}" method="POST">
  @csrf
  @foreach ($fornecedores as $fornecedor)
    <div class="input-group input-group-lg">
      <span class="input-group-text" id="inputGroup-sizing-lg">Nome</span>
      <input type="text" name="nome_fornecedor" class="form-control" aria-label="Sizing example input" value="{{$fornecedor->nome_fornecedor}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">CNPJ</span>
      <input type="text" name="cnpj" class="form-control" aria-label="Sizing example input" id="cnpj" value="{{$fornecedor->cnpj}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">CEP</span>
      <input type="text" name="cep" class="form-control" aria-label="Sizing example input" id="cep" value="{{$fornecedor->cep}}">
    </div>
    <div class="input-group input-group-lg">
      <span class="input-group-text" id="inputGroup-sizing-lg">Logradouro</span>
      <input type="text" name="logradouro" class="form-control" aria-label="Sizing example input" id="endereco" value="{{$fornecedor->logradouro}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Bairro</span>
      <input type="text" name="bairro" class="form-control" aria-label="Sizing example input" id="bairro" value="{{$fornecedor->bairro}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">N. Casa</span>
      <input type="number" name="numero_casa" class="form-control" aria-label="Sizing example input" value="{{$fornecedor->numero_casa}}">
    </div>
    <div class="input-group input-group-lg">
        <span class="input-group-text" id="inputGroup-sizing-lg">Email</span>
        <input type="email" name="email" class="form-control" aria-label="Sizing example input" value="{{$fornecedor->email}}">
        <span class="input-group-text" id="inputGroup-sizing-lg">Cidade</span>
        <input type="text" name="cidade" class="form-control" aria-label="Sizing example input" id="cidade" value="{{$fornecedor->cidade}}">
        <span class="input-group-text" id="inputGroup-sizing-lg">UF</span>
        <input type="text" name="uf" class="form-control" aria-label="Sizing example input" id="uf" value="{{$fornecedor->uf}}">
      </div>
 @endforeach
    @foreach ($telefones as $telefone)
        <div class="input-group input-group-lg w-50">
            <span class="input-group-text" id="inputGroup-sizing-lg">DDD</span>
            <input type="text" name="ddd" class="form-control" aria-label="Sizing example input" value="{{$telefone->ddd}}">
            <span class="input-group-text" id="inputGroup-sizing-lg">Telefone</span>
            <input type="text" name="telefone" class="form-control" aria-label="Sizing example input" value="{{$telefone->telefone}}">
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
              <input type="checkbox" class="btn-check" id="btncheck1" name="principal" value="1" autocomplete="off" {{ $telefone->principal == 1 ? 'checked' : '' }}>
              <label class="btn btn-outline-primary @if ($telefone->principal == 1) 'btn-primary' @else 'btn-outline-primary' @endif" for="btncheck1">Principal</label>
            </div>
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
              <input type="checkbox" class="btn-check" id="btncheck2" name="whatsapp" value="1" autocomplete="off" {{ $telefone->whatsapp == 1 ? 'checked' : '' }}>
              <label class="btn btn-outline-primary @if ($telefone->whatsapp == 1) 'btn-primary' @else 'btn-outline-primary' @endif" for="btncheck2">Whatsapp</label>
          </div>
          <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
              <input type="checkbox" class="btn-check" id="btncheck3" name="telegram" value="1" autocomplete="off" {{ $telefone->telegram == 1 ? 'checked' : '' }}>
              <label class="btn btn-outline-primary @if ($telefone->telegram == 1) 'btn-primary' @else 'btn-outline-primary' @endif " for="btncheck3">Telegram</label>
          </div>    
        </div>
    @endforeach
</div> 
    <button class="btn btn-primary m-1" type="submit">Editar</button>
</form>
@endsection
