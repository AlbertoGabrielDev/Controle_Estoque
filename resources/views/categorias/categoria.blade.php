@extends('layouts.principal')

@section('conteudo')
<div class="bg-gray-100 font-sans text-2xl">

  <a class="block text-gray-500 py-2.5 px-4 relative mx-5 my-4 w-48 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white"href="{{route('categoria.index')}}">
    <i class="fas fa-home mr-2"></i>Index
  </a>
  <div class="grid grid-cols-6 gap-4">
    @foreach ($categorias as $categoria) 
    <div class="row-span-2 col-span-2 flex w-96 flex-col m-5 rounded-xl bg-white bg-clip-border text-gray-700 shadow-md">
      <div class="relative mx-4 mt-2 h-56 overflow-hidden rounded-xl bg-blue-gray-500 bg-clip-border text-white shadow-lg shadow-blue-gray-500/40">
        <img src="/img/categorias/{{$categoria->imagem}}" alt="{{$categoria->nome_categoria}}" layout="fill"/>
      </div>
      <div class="p-6">
        <h5 class="mb-2 block font-sans text-xl font-semibold leading-snug tracking-normal text-blue-gray-900 antialiased">
          {{ $categoria->nome_categoria }}
        </h5>
      </div>
      <div class="p-6 pt-0">
        <button
          class="select-none rounded-lg bg-pink-500 py-3 px-6 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-pink-500/20 transition-all hover:shadow-lg hover:shadow-pink-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
          type="button"
          data-ripple-light="true"
        >
          @if ($categoria->produtos->count() > 0)
            <a href="{{ route('categorias.produto', $categoria->id_categoria) }}" class="btn btn-primary">Ver Produtos</a>
          @else
            <p>Nenhum produto disponível nesta categoria.</p>
          @endif
        </button>
      </div>
    </div>
    @endforeach
  </div>
</div>

@endsection
