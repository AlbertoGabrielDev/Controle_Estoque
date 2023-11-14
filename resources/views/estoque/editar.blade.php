@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <div class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center">Editar Estoque</div> 
    <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('estoque.index')}}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
   
    <form action="{{route('estoque.salvarEditar', $estoques->first()->id_estoque)}}" method="POST">
        @csrf
        @foreach ($estoques as $estoque)
            <div class="grid md:grid-cols-3 md:gap-6 py-4">
                <div class="relative z-0 w-full mb-6 group">
                    <input type="text" name="preco_custo" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" aria-label="Sizing example input" value="{{$estoque->preco_custo}}">
                    <label class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6" id="inputGroup-sizing-lg">Preço Custo</label>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <input type="text" name="preco_venda" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" aria-label="Sizing example input" value="{{$estoque->preco_venda}}">
                    <label class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6" id="inputGroup-sizing-lg">Preço Venda</label>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <input type="number" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer-lg" required name="quantidade_aviso" value="{{$estoque->quantidade_aviso}}">
                    <label class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6" id="inputGroup-sizing-lg">Quantidade para aviso</label>
                </div>
            </div>
            <div class="grid md:grid-cols-2 md:gap-6 py-4">
                <div class="relative z-0 w-full mb-6 group">
                    <label class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6" id="inputGroup-sizing-lg">Localização</label>
                    <input type="text" name="localizacao" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" aria-label="Sizing example input" value="{{$estoque->localizacao}}">
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <label class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 w-75" id="inputGroup-sizing-lg">Fornecedor</label>
                    <select class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer-lg w-75" name="fornecedor">
                        @foreach ($fornecedores as $fornecedor)
                            <option value="{{ $fornecedor->id_fornecedor }}">{{ $fornecedor->nome_fornecedor }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endforeach
        <button type="submit" class="block text-gray-500 py-2.5 relative my-4 w-48 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white">
            <i class="fas fa-plus mr-2"></i> Editar Produtos
        </button>
    </form>
</div>
@endsection
