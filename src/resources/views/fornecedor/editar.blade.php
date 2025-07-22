@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full max-w-4xl mx-auto">
  <div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-semibold text-slate-700">Editar Fornecedor</h1>
    <a href="{{ route('fornecedor.index') }}"
      class="flex items-center px-4 py-2 bg-gray-100 hover:bg-cyan-500 text-gray-600 hover:text-white rounded-md transition-colors">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
  </div>

  <form action="{{ route('fornecedor.salvarEditar', $fornecedores->first()->id_fornecedor) }}" method="POST" class="space-y-6">
    @csrf
    @foreach ($fornecedores as $fornecedor)
    <div class="form-group">
      <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
      <input type="text"
        name="nome_fornecedor"
        class="w-full px-3 py-2 border rounded-md bg-gray-100 cursor-not-allowed"
        value="{{ $fornecedor->nome_fornecedor }}"
        disabled>
    </div>
    @endforeach

    @foreach ($telefones as $telefone)
    <div class="grid md:grid-cols-2 gap-6">
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-1">DDD</label>
        <input type="text"
          name="ddd"
          class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
          value="{{ $telefone->ddd }}">
      </div>

      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
        <input type="text"
          name="telefone"
          class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
          value="{{ $telefone->telefone }}">
      </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
      <!-- Checkbox Principal -->
      <div class="group relative">
        <input type="checkbox"
          name="principal"
          value="1"
          id="principal"
          class="absolute opacity-0 peer"
          {{ $telefone->principal == 1 ? 'checked' : '' }}>
        <label for="principal"
          class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition-all
                      bg-white hover:border-cyan-300 peer-checked:border-red-500 peer-checked:bg-red-50
                      peer-focus:ring-2 peer-focus:ring-red-200">
          <div class="flex items-center space-x-3">

            <span class="text-gray-700 font-medium">Principal</span>
          </div>
        </label>
      </div>

      <!-- Checkbox WhatsApp -->
      <div class="group relative">
        <input type="checkbox"
          name="whatsapp"
          id="whatsapp"
          value="1"
          class="absolute opacity-0 peer"
          {{ $telefone->whatsapp == 1 ? 'checked' : '' }}>
        <label for="whatsapp"
          class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition-all
                      bg-white hover:border-cyan-300 peer-checked:border-green-500 peer-checked:bg-green-50
                      peer-focus:ring-2 peer-focus:ring-green-200">
          <div class="flex items-center space-x-3">

            <span class="text-gray-700 font-medium">Whatsapp</span>
          </div>
        </label>
      </div>

      <!-- Checkbox Telegram -->
      <div class="group relative">
        <input type="checkbox"
          name="telegram"
          id="telegram"
          value="1"
          class="absolute opacity-0 peer"
          {{ $telefone->telegram == 1 ? 'checked' : '' }}>
        <label for="telegram"
          class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition-all
                      bg-white hover:border-cyan-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50
                      peer-focus:ring-2 peer-focus:ring-indigo-200">
          <div class="flex items-center space-x-3">

            <span class="text-gray-700 font-medium">Telegram</span>
          </div>
        </label>
      </div>
    </div>
    @endforeach

    <button type="submit"
      class="w-full md:w-auto px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-md transition-colors">
      <i class="fas fa-save mr-2"></i>Salvar Alterações
    </button>
  </form>
</div>
@endsection