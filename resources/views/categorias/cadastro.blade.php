@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
<div class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center ">Cadastro de Categoria</div> 

<form action="{{route('categoria.inserirCategoria')}}" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="flex items-center w-full mt-2 p-4">
      <input type="text" name="categoria" required class="pl-10 pr-4 py-2 rounded-full border border-gray-300 w-2/12 text-sm placeholder-gray-400" placeholder="Categoria" />
  </div>
  <div class="flex items-center w-full mt-2 p-4">
    <div class="relative w-full">
      <label class="mb-2 inline-block text-neutral-700" for="file_input">Importar Imagem</label>
      <input type="file" name="imagem" required class="relative m-0 block w-2/12 min-w-0 flex-auto rounded border px-3 py-[0.32rem] text-base font-normal file:-mx-3 file:-my-[0.32rem] file:border-0 file:border-solid file:border-inherit file:px-3 file:py-[0.32rem] file:text-neutral-700 file:[margin-inline-end:0.75rem] file:cursor-pointer cursor-pointer dark:file:bg-neutral-700 dark:file:text-neutral-100 " />
    </div>
  </div>
  <button class="block text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-48 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white">
    <i class="fas fa-plus mr-2"></i> Criar Categoria
  </button>
    
</form>
</div>
@endsection