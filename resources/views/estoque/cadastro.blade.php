@extends('layouts.principal')

@section('conteudo')
<div class="estoque_espacamento"></div>

<form action="{{route('estoque.inserirEstoque')}}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="quantidade" placeholder="Quantidade">
        </div>
        <div class="col-md-4">
          <input type="number" class="form-control form-control-lg w-75" required name="preco_custo"  placeholder="Preco Custo">
        </div>
        <div class="col-md-4">
          <input type="number" class="form-control form-control-lg w-75" required name="preco_venda"  placeholder="Preço Venda">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
          <input type="date" class="form-control form-control-lg w-75" required name="data_chegada" placeholder="Data Chegada">
        </div>
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="lote"  placeholder="Lote">
        </div>
        <div class="col-md-4">
          <input type="text" class="form-control form-control-lg w-75" required name="localizacao"  placeholder="Localização">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <select class="form-control form-control-lg w-75" name="categoria" required>
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
        <select class="form-control form-control-lg w-75" name="categoria" required>
            <option value="">Selecione um Fornecedor</option>
            @foreach ($fornecedor as $fornecedores)
                <option value="{{ $fornecedores->id_fornecedor }}">{{ $fornecedores->nome_fornecedor }}</option>
            @endforeach
        </select>
        </div>
    </div>
    <div class="div_criar_categoria2">
        <button class="button_criar_categoria2" type="submit">Criar Estoque</button>     
    </div>
</form>
@endsection