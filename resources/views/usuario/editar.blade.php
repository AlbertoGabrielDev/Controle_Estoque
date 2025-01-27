@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full">
  <div class="mx-auto m-5 text-4xl font-medium text-slate-700 flex justify-center">Editar Usuário</div>
  <a class=" text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-1/12 rounded hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('usuario.index')}}">
    <i class="fa fa-angle-left mr-2"></i>Voltar
  </a>


  <form action="{{ route('usuario.salvarEditar', $usuarios->first()->id) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @foreach ($usuarios as $usuario)
    <div class="grid md:grid-cols-3 md:gap-6 py-4">
      <div class="flex items-center w-full mt-2 p-4">
        <div class="relative w-full">
          <label class="mb-2 inline-block text-neutral-700" for="file_input">Importar Imagem</label>
          <input type="file" name="imagem" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>
      </div>
      <div class="relative z-0 w-full mb-6 group">
        <input type="text" name="name" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" value="{{ $usuario->name }}">
        <label for="name" class="peer-focus:font-medium text-gray-500">Nome</label>
      </div>
      <div class="relative z-0 w-full mb-6 group">
        <input type="email" name="email" class="block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" value="{{ $usuario->email }}">
        <label for="email" class="peer-focus:font-medium text-gray-500">Email</label>
      </div>
    </div>
  @endforeach
  <button type="submit" class="block text-gray-500 py-2.5 my-4 w-48 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white">
    <i class="fas fa-save mr-2"></i> Salvar Alterações
  </button>
</form>

</div>
@endsection