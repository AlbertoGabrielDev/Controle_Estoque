@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full max-w-4xl mx-auto">
  <!-- Cabeçalho -->
  <div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-semibold text-slate-700">Editar Marca</h1>
    <a href="{{ route('marca.index') }}"
      class="flex items-center px-4 py-2 bg-gray-100 hover:bg-cyan-500 text-gray-600 hover:text-white rounded-md transition-colors">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
  </div>

  <form action="{{ route('marca.editar', $marcas->first()->id_marca) }}" method="POST" class="space-y-6">
    @csrf
    @foreach ($marcas as $marca)
    <div class="form-group">
      <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Marca</label>
      <input type="text"
        name="nome_marca"
        class="w-full px-3 py-2 border rounded-md bg-gray-100 "
        value="{{ $marca->nome_marca }}">
    </div>
    @endforeach

    <button type="submit"
      class="w-full md:w-auto px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-md transition-colors">
      <i class="fas fa-save mr-2"></i>Salvar Alterações
    </button>
  </form>
</div>
@endsection