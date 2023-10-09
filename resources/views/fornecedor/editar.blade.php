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
