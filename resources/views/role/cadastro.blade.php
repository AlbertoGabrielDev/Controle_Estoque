@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-6 rounded-md shadow-md w-full">
  <h5 class="text-center text-2xl font-semibold text-gray-700 mb-6">Cadastro de usuário</h5>
  <a class=" text-gray-600 py-2 px-4 rounded-lg bg-gray-100 hover:bg-cyan-400 hover:text-white transition" href="{{route('roles.index')}}">
    <i class="fa fa-angle-left mr-2"></i>Voltar
  </a>
  <form action="{{route('roles.inserirRole')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="border-b border-gray-900/10 pb-12">

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-4">
          <label for="name" class="block text-sm/6 font-medium text-gray-900">Nome</label>
          <div class="mt-2">
            <input type="text" name="name" id="name" autocomplete="given-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm/6" required>
          </div>
        </div>

      
      </div>
    </div>
    <button type="submit" class="text-gray-600 py-2 px-4 mt-4 rounded-lg bg-gray-100 hover:bg-cyan-400 hover:text-white transition">
      <i class="fas fa-plus mr-2"></i> Cadastrar role
    </button>
</div>

</form>
</div>
@endsection
