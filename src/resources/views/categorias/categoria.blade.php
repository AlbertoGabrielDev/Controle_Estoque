@extends('layouts.principal')

@section('conteudo')
<div class="min-h-screen bg-gray-50 p-4 md:p-8">
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
      <h1 class="text-3xl font-bold text-gray-800">Categorias</h1>
      <a href="{{ route('categoria.index') }}" class="flex items-center space-x-2 rounded-lg bg-white px-4 py-2 text-cyan-600 shadow-md transition-all hover:bg-cyan-50">
        <i class="fas fa-home"></i>
        <span>Index</span>
      </a>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      @foreach ($categorias as $categoria)
      <div class="flex flex-col overflow-hidden rounded-xl bg-white shadow-lg transition-all duration-300 hover:shadow-xl">
        <!-- Image Container -->
        <div class="relative h-48 w-full bg-gray-100">
          <img 
            src="{{ $categoria->imagem ? '/img/categorias/'.$categoria->imagem : 'http://plone.ufpb.br/labeet/contents/paginas/acervo-brazinst/copy_of_cordofones/udecra/sem-imagem.jpg/image_view_fullscreen' }}" 
            alt="{{ $categoria->nome_categoria }}"
            class="h-full w-full object-cover object-center"
            loading="lazy"
          >
        </div>

        <!-- Content -->
        <div class="flex flex-1 flex-col p-6">
          <h3 class="mb-4 text-xl font-semibold text-gray-800">{{ $categoria->nome_categoria }}</h3>
          
          <div class="mt-auto">
            @if($categoria->produtos->count() > 0)
              <a href="{{ route('categorias.produto', $categoria->id_categoria) }}" 
                 class="block w-full rounded-lg bg-cyan-500 px-4 py-2 text-center text-white transition-all hover:bg-cyan-600">
                Ver Produtos ({{ $categoria->produtos->count() }})
              </a>
            @else
              <div class="rounded-lg bg-gray-100 px-4 py-2 text-center text-gray-500">
                Nenhum produto disponível
              </div>
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection