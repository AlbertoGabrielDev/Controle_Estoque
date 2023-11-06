@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <div class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center ">Editar Fornecedores</div> 
  <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('fornecedor.index')}}">
    <i class="fa fa-angle-left mr-2"></i>Voltar
  </a>

  <form action="{{route('fornecedor.salvarEditar', $fornecedores->first()->id_fornecedor)}}" method="POST">
    @csrf
    @foreach ($fornecedores as $fornecedor)
      <div class="relative z-0 w-2/12 mb-6 group mt-4">
        <input type="text" name="nome_fornecedor" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" disabled aria-label="Sizing example input" aria-label="Sizing example input" value="{{$fornecedor->nome_fornecedor}}">
        <label for="text" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nome</label>
      </div>
    @endforeach
    @foreach ($telefones as $telefone)
    <div class="grid md:grid-cols-2 md:gap-6 py-4">
        <div class="relative z-0 w-full mb-6 group">
          <input type="text" name="ddd" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" aria-label="Sizing example input" value="{{$telefone->ddd}}">
          <label for="text" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">DDD</label>
        </div>
        <div class="relative z-0 w-full mb-6 group">
          <input type="text" name="telefone" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" aria-label="Sizing example input" value="{{$telefone->telefone}}">
          <label for="text" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Telefone</label>
        </div>   
    </div> 
    <div class="grid md:grid-cols-3 md:gap-6 py-4">
      <div class="inline-flex items-center">
        <input type="checkbox" class="relative h-5 w-5 cursor-pointer appearance-none rounded-md border before:absolute before:top-2/4 before:left-2/4 before:block before:h-12 before:w-12 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-blue-gray-500 before:opacity-0 before:transition-opacity checked:border-red-500 checked:bg-red-500 checked:before:bg-red-500 hover:before:opacity-10" id="btncheck1" name="principal" value="1" autocomplete="off" {{ $telefone->principal == 1 ? 'checked' : '' }}>
        <label class="relative flex cursor-pointer items-center rounded-full p-3" for="btncheck1">Principal</label>
      </div>  
      <div class="inline-flex items-center">
        <input type="checkbox" class="relative h-5 w-5 cursor-pointer appearance-none rounded-md border before:absolute before:top-2/4 before:left-2/4 before:block before:h-12 before:w-12 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-blue-gray-500 before:opacity-0 before:transition-opacity checked:border-green-500 checked:bg-green-500 checked:before:bg-green-500 hover:before:opacity-10" id="btncheck2" name="whatsapp" value="1" autocomplete="off" {{ $telefone->whatsapp == 1 ? 'checked' : '' }}>
        <label class="relative flex cursor-pointer items-center rounded-full p-3" for="btncheck2">Whatsapp</label>
      </div>
      <div class="inline-flex items-center">
        <input type="checkbox" class="relative h-5 w-5 cursor-pointer appearance-none rounded-md border before:absolute before:top-2/4 before:left-2/4 before:block before:h-12 before:w-12 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-blue-gray-500 before:opacity-0 before:transition-opacity checked:border-indigo-500 checked:bg-indigo-500 checked:before:bg-indigo-500 hover:before:opacity-10" id="btncheck3" name="telegram" value="1" autocomplete="off" {{ $telefone->telegram == 1 ? 'checked' : '' }}>
        <label class="relative flex cursor-pointer items-center rounded-full p-3" for="btncheck3">Telegram</label>
      </div>  
    </div>
    @endforeach
    <button type="submit" class="block text-gray-500 py-2.5 relative my-4 w-48 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white">
      <i class="fas fa-plus mr-2"></i> Editar Fornecedor
    </button>
  </form>
</div>
@endsection
