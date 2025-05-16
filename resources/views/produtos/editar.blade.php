@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-4 rounded-md w-full max-w-4xl mx-auto">
  <!-- Cabeçalho -->
  <div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-semibold text-slate-700">Editar Produto</h1>
    <a href="{{ route('produtos.index') }}"
      class="flex items-center px-4 py-2 bg-gray-100 hover:bg-cyan-500 text-gray-600 hover:text-white rounded-md transition-colors">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
  </div>

  @foreach ($produtos as $produto)
  <form action="{{route('produtos.salvarEditar',$produto->id_produto)}}" method="POST" class="space-y-6">
    @csrf
    <!-- Primeira Linha -->
    <div class="grid md:grid-cols-2 gap-6">
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-1">Código do Produto</label>
        <input type="number" 
          name="cod_produto"
          class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
          value="{{$produto->cod_produto}}">
      </div>

      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
        <input type="text" 
          name="nome_produto"
          class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
          value="{{$produto->nome_produto}}">
      </div>
    </div>

    <!-- Segunda Linha -->
    <div class="grid md:grid-cols-2 gap-6">
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <input type="text" 
          name="descricao"
          class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
          value="{{$produto->descricao}}">
      </div>

      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-1">Informações Nutricionais</label>
        <input type="text" 
          name="inf_nutrientes"
          class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
          value="{{json_decode($produto->inf_nutrientes)}}">
      </div>
    </div>

    <!-- Terceira Linha -->
    <div class="grid md:grid-cols-2 gap-6">
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-1">QR Code</label>
        <input type="text" 
          name="qrcode"
          class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
          value="{{$produto->qrcode}}">
      </div>

      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-1">Unidade de Medida</label>
        <input type="text" 
          class="w-full px-3 py-2 border rounded-md bg-gray-100 cursor-not-allowed"
          value="{{$produto->unidade_medida}}"
          disabled>
      </div>
    </div>

    <button type="submit" 
      class="w-full md:w-auto px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-md transition-colors">
      <i class="fas fa-save mr-2"></i>Salvar Alterações
    </button>
  </form>
  @endforeach
</div>
@endsection