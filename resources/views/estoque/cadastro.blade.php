@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <div class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center ">Cadastro de Produtos</div> 
  <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('estoque.index')}}">
    <i class="fa fa-angle-left mr-2"></i>Voltar
  </a>
  <form action="{{route('estoque.inserirEstoque')}}" method="POST">
      @csrf
      <div class="grid md:grid-cols-3 md:gap-6 py-4">
          <div class="relative z-0 w-full mb-6 grou">
            <input type="text" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required name="quantidade" placeholder=" ">
            <label for="text" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Quantidade</label>
          </div>
          <div class="relative z-0 w-full mb-6 grou">
            <input type="text" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required name="preco_custo"  placeholder=" ">
            <label for="text" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Preço Custo</label>
          </div>
          <div class="relative z-0 w-full mb-6 grou">
            <input type="text" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required name="preco_venda"  placeholder=" ">
            <label for="text" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Preço Venda</label>
          </div>
      </div>
      <div class="grid md:grid-cols-3 md:gap-6 py-4">
        <div class="relative z-0 w-full mb-6 grou">
            <input type="number" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required name="quantidade_aviso" placeholder=" ">
            <label for="text" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Quantidade para Aviso</label>
        </div>
        <div class="relative z-0 w-full mb-6 grou">
          <input type="text" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required name="lote"  placeholder=" ">
          <label for="text" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Lote</label>
        </div>
        <div class="relative z-0 w-full mb-6 grou">
          <input type="text" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required name="localizacao"  placeholder=" ">
          <label for="text" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Localização</label>
        </div>
      </div>
      <div class="grid md:grid-cols-2 md:gap-6 py-4">
        <div class="d-flex align-items-center">
          <label class="text-sm text-gray-500">Data Vencimento</label>
          <input type="date" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required name="validade">
        </div>
        <div class="d-flex align-items-center">
          <label class="text-sm text-gray-500">Data Chegada</label>
          <input type="date" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required name="data_chegada" placeholder="Data Chegada">
        </div>
      </div>
    <div class="grid md:grid-cols-3 md:gap-6 py-4">
      <div class="col-md-4">
        <select class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" name="marca" required>
          <option value="">Selecione uma Marca</option>
          @foreach ($marcas as $marca)
            <option value="{{ $marca->id_marca }}">{{ $marca->nome_marca }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <select class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" name="nome_produto" required>
          <option>Selecione um Produto</option>
          @foreach ($produtos as $produto)
            <option value="{{ $produto->id_produto }}">{{ $produto->nome_produto }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <select class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" name="fornecedor" required>
          <option>Selecione um Fornecedor</option>
          @foreach ($fornecedores as $fornecedor)
            <option value="{{ $fornecedor->id_fornecedor }}">{{ $fornecedor->nome_fornecedor }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <button type="submit" class="block text-gray-500 py-2.5 relative my-4 w-48 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white">
      <i class="fas fa-plus mr-2"></i> Criar Produto
    </button>    
  </form>
</div>
@endsection