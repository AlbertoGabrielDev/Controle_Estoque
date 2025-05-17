@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full max-w-4xl mx-auto">
  <!-- Cabeçalho -->
  <div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-semibold text-slate-700">Editar Categoria</h1>
    <a href="{{ route('categoria.index') }}"
      class="flex items-center px-4 py-2 bg-gray-100 hover:bg-cyan-500 text-gray-600 hover:text-white rounded-md transition-colors">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
  </div>

  <form action="{{ route('categorias.editar', $categorias->first()->id_categoria) }}" method="POST" class="space-y-6">
    @csrf
    @if($categorias->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Categoria</label>
        <input type="text"
          name="nome_categoria"
          class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
          value="{{ old('nome_categoria', $categorias->first()->nome_categoria) }}"
          required>
        @error('nome_categoria')
          <div class="mt-1 text-red-500 text-sm">{{ $message }}</div>
        @enderror
      </div>
    </div>
    @endif

    <button type="submit"
      class="w-full md:w-auto px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
      <i class="fas fa-save mr-2"></i>Salvar Alterações
    </button>
  </form>
</div>
@endsection