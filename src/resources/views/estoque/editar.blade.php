@extends('layouts.principal')

@section('conteudo')
    <div class="bg-white p-4 rounded-md w-full max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-semibold text-slate-700">Editar Estoque</h1>
            <a href="{{ route('estoque.index') }}"
               class="flex items-center px-4 py-2 bg-gray-100 hover:bg-cyan-500 text-gray-600 hover:text-white rounded-md transition-colors">
                <i class="fa fa-angle-left mr-2"></i>Voltar
            </a>
        </div>

        <form action="{{ route('estoque.salvarEditar', $estoque->id_estoque) }}" method="POST" class="space-y-6" id="form-estoque-editar">
            @csrf
            @method('PUT')

            {{-- Hidden p/ impostos (o backend recalcula de qualquer maneira no salvar) --}}
            <input type="hidden" name="imposto_total" id="imposto_total" value="{{ $previewVM['__totais']['total_impostos'] ?? 0 }}">
            <input type="hidden" name="impostos_json" id="impostos_json" value='@json($impostos_raw ?? [])'>

            {{-- Produto fixo / somente leitura --}}
            <label class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
            <input type="text" class="w-full px-3 py-2 border rounded-md bg-gray-100 cursor-not-allowed"
                   value="{{ $estoque->produtos->nome_produto }}" disabled>
            <input type="hidden" name="id_produto_fk" value="{{ $estoque->id_produto_fk }}">

            {{-- Primeira Linha --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço Custo</label>
                    <input type="number" step="0.01" name="preco_custo"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                           value="{{ old('preco_custo', $estoque->preco_custo) }}">
                    @error('preco_custo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço Venda</label>
                    <input type="number" step="0.01" name="preco_venda"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                           value="{{ old('preco_venda', $estoque->preco_venda) }}">
                    @error('preco_venda') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade Alerta</label>
                    <input type="number" name="quantidade_aviso"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                           value="{{ old('quantidade_aviso', $estoque->quantidade_aviso) }}">
                    @error('quantidade_aviso') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Segunda Linha --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                    <input type="text" name="localizacao"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                           value="{{ old('localizacao', $estoque->localizacao) }}">
                    @error('localizacao') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lote</label>
                    <input type="text" name="lote"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                           value="{{ old('lote', $estoque->lote) }}">
                    @error('lote') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Terceira Linha --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Chegada</label>
                    <input type="date" name="data_chegada"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                           value="{{ old('data_chegada', optional($estoque->data_chegada)->format('Y-m-d')) }}">
                    @error('data_chegada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Validade</label>
                    <input type="date" name="validade"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                           value="{{ old('validade', optional($estoque->validade)->format('Y-m-d')) }}">
                    @error('validade') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade Atual</label>
                    <input type="number" name="quantidade"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-cyan-500"
                           value="{{ old('quantidade', $estoque->quantidade) }}">
                    @error('quantidade') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Bloco de Impostos (preview) --}}
          
            <div class="mt-6 border rounded p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-700">Impostos estimados</h3>
                </div>
                <div id="impostosArea" class="mt-3 text-sm text-slate-700">
                    @include('estoque.partials._impostos', ['vm' => $previewVM ?? null])
                </div>
            </div>

            <button type="submit"
                    class="w-full md:w-auto px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-md transition-colors">
                <i class="fas fa-save mr-2"></i>Salvar Alterações
            </button>
        </form>
    </div>
@endsection
