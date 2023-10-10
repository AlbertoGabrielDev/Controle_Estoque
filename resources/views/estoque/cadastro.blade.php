@extends('layouts.principal')

@section('conteudo')
  <h1 class="h1 text-center">Cadastro de Estoque</h1>
  <a class="btn btn-primary m-3" href="{{route('estoque.index')}}">Voltar</a>
<form action="{{route('estoque.inserirEstoque')}}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="quantidade" placeholder="Quantidade">
        </div>
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="preco_custo"  placeholder="Preco Custo">
        </div>
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="preco_venda"  placeholder="Preço Venda">
        </div>
        
    </div>
    <div class="row">
      <div class="col-md-4">
          <input type="number" class="form-control form-control-lg w-75" required name="quantidade_aviso" placeholder="Quantidade para aviso">
      </div>
      <div class="col-md-4">
        <input type="text" class="form-control form-control-lg w-75" required name="lote"  placeholder="Lote">
      </div>
      <div class="col-md-4">
        <input type="text" class="form-control form-control-lg w-75" required name="localizacao"  placeholder="Localização">
      </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        <div class="d-flex align-items-center">
          <span>Data Vencimento</span>
          <input type="date" class="form-control form-control-lg w-75" required name="validade">
        </div>
      </div>
      <div class="col-md-3">
          <div class="d-flex align-items-center">
            <span>Data Chegada</span>
          <input type="date" class="form-control form-control-lg w-75" required name="data_chegada" placeholder="Data Chegada">
          </div>
        </div>
    </div>
  <div class="row">
    <div class="col-md-4">
      <select class="form-control form-control-lg w-75" name="marca" required>
        <option value="">Selecione uma Marca</option>
        @foreach ($marca as $marcas)
            <option value="{{ $marcas->id_marca }}">{{ $marcas->nome_marca }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <select class="form-control form-control-lg w-75" name="nome_produto" required>
        <option value="">Selecione um Produto</option>
        @foreach ($produto as $produtos)
            <option value="{{ $produtos->id_produto }}">{{ $produtos->nome_produto }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <select class="form-control form-control-lg w-75" name="fornecedor" required>
        <option value="">Selecione um Fornecedor</option>
        @foreach ($fornecedores as $fornecedor)
            <option value="{{ $fornecedor->id_fornecedor }}">{{ $fornecedor->nome_fornecedor }}</option>
        @endforeach
      </select>
    </div>
  </div>
    <button class="btn btn-primary m-1" type="submit">Criar Estoque</button>     
</form>
@endsection