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
      <form action="{{route('produtos.salvarEditar', $produto->id_produto)}}" method="POST" class="space-y-6">
        @csrf
        <!-- Primeira Linha -->
        <div class="grid md:grid-cols-2 gap-6">
          <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-1">Código do Produto</label>
            <input type="text" name="cod_produto"
              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500" value="{{$produto->cod_produto}}">
          </div>

          <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
            <input type="text" name="nome_produto"
              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
              value="{{$produto->nome_produto}}">
          </div>
        </div>

        <!-- Segunda Linha -->
        <div class="grid md:grid-cols-2 gap-6">
          <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <input type="text" name="descricao" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
              value="{{$produto->descricao}}">
          </div>

          <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Informações Nutricionais (JSON)
            </label>
            <textarea name="inf_nutriente" rows="4"
              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500">{{ 
        old('inf_nutriente', json_encode($produto->inf_nutriente ?? $produto->inf_nutrientes ?? null, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) 
      }}</textarea>
            @error('inf_nutriente')
              <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Terceira Linha -->
        <div class="grid md:grid-cols-2 gap-6">
          <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-1">QR Code</label>
            <input type="text" name="qrcode" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
              value="{{$produto->qrcode}}">
          </div>

          <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-1">Unidade de Medida</label>
            <input type="text" class="w-full px-3 py-2 border rounded-md bg-gray-100 cursor-not-allowed"
              value="{{$produto->unidade_medida}}" disabled>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
            <select name="id_categoria_fk" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
              required>
              <option value="">Selecione uma Categoria</option>
              @foreach ($categorias as $cat)
                <option value="{{ $cat->id_categoria }}" @selected(($produto->categorias->first()->id_categoria ?? $produto->id_categoria_fk ?? null) == $cat->id_categoria)>
                  {{ $cat->nome_categoria }}
                </option>
              @endforeach
            </select>
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