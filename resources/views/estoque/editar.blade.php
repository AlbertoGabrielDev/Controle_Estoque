@extends('layouts.principal')

@section('conteudo')
<div class="container d-flex justify-content-between align-items-center">
  <div class="mx-auto">
    <h1 class="card-title">Editar Estoque</h1>
  </div>
  <div>
    <a class="btn btn-primary" href="{{route('categoria.inicio')}}">Voltar</a>
  </div>
</div>
<form action="{{route('estoque.salvarEditar', $estoques->first()->id_estoque)}}" method="POST">
  @csrf
  @foreach ($estoques as $estoque)
    <div class="input-group input-group-lg">
      <span class="input-group-text" id="inputGroup-sizing-lg">Preço Custo</span>
      <input type="number" name="preco_custo" class="form-control" aria-label="Sizing example input" value="{{$estoque->preco_custo}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Preço Venda</span>
      <input type="number" name="preco_venda" class="form-control" aria-label="Sizing example input" value="{{$estoque->preco_venda}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Quantidade</span>
      <input type="number" name="quantidade" class="form-control" aria-label="Sizing example input" value="{{$estoque->quantidade}}">
    </div>
    <div class="input-group input-group-lg">
      <span class="input-group-text" id="inputGroup-sizing-lg">Data Chegada</span>
      <input type="date" name="data_chegada" class="form-control" aria-label="Sizing example input" value="{{$estoque->data_chegada}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Lote</span>
      <input type="text" name="lote" class="form-control" aria-label="Sizing example input" value="{{$estoque->lote}}">
      <span class="input-group-text" id="inputGroup-sizing-lg">Localização</span>
      <input type="text" name="localizacao" class="form-control" aria-label="Sizing example input" value="{{$estoque->localizacao}}">
    </div>
    <div class="input-group input-group-lg">
        <div class="col-md-4">
            <select class="form-control form-control-lg w-75" name="marca" >
                <option value="">Selecione uma Marca</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id_marca }}">{{ $marca->nome_marca }}</option>
                @endforeach
            </select>
       </div>
       <div class="col-md-4">
            <select class="form-control form-control-lg w-75" name="nome_produto" >
                <option value="">Selecione um Produto</option>
                @foreach ($produtos as $produto)
                    <option value="{{ $produto->id_produto }}">{{ $produto->nome_produto }}</option>
                @endforeach
            </select>
       </div>
       
       <div class="col-md-4">
        <select class="form-control form-control-lg w-75" name="fornecedor" >
            <option value="">Selecione um Fornecedor</option>
            @foreach ($fornecedores as $fornecedor)
                <option value="{{ $fornecedor->id_fornecedor }}">{{ $fornecedor->nome_fornecedor }}</option>
            @endforeach
        </select>
        </div>  
    </div>
 @endforeach
</div> 
    <button class="" type="submit">Editar</button>
</form>

@endsection
