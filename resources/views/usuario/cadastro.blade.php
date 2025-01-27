@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-6 rounded-md shadow-md w-full">
  <h5 class="text-center text-2xl font-semibold text-gray-700 mb-6">Cadastro de usuário</h5>
  <a class=" text-gray-600 py-2 px-4 rounded-lg bg-gray-100 hover:bg-cyan-400 hover:text-white transition" href="{{route('usuario.index')}}">
    <i class="fa fa-angle-left mr-2"></i>Voltar
  </a>
  <form action="{{route('usuario.inserirUsuario')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="border-b border-gray-900/10 pb-12">

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-4">
          <label for="first-name" class="block text-sm/6 font-medium text-gray-900">Nome</label>
          <div class="mt-2">
            <input type="text" name="name" id="name" autocomplete="given-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm/6" required>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label for="email" class="block text-sm/6 font-medium text-gray-900">Email</label>
          <div class="mt-2">
            <input id="email" name="email" type="email" autocomplete="email" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm/6" required>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label for="password" class="block text-sm/6 font-medium text-gray-900">Senha</label>
          <div class="mt-2">
            <input type="password" name="password" id="password" autocomplete="family-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm/6" required>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label for="id_unidade" class="block text-sm/6 font-medium text-gray-900">Unidade</label>
          <div class="mt-2 grid grid-cols-1">
            <select id="id_unidade" name="id_unidade" required autocomplete="country-name" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm/6">
              <!-- Opção inicial -->
              <option value="" disabled selected>Selecione uma unidade</option>

              <!-- Opções do select -->
              @foreach($units as $unit)
              <option value="{{ $unit->id_unidade }}">{{ $unit->nome }}</option>
              @endforeach
            </select>
            <svg class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon">
              <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
        <div class="col-span-full">
          <label for="photo" class="block text-sm/6 font-medium text-gray-900">Photo</label>
          <div class="mt-2 flex items-center gap-x-3">
            <!-- Prévia da imagem selecionada, com o tamanho igual ao do ícone -->
            <img id="image-preview" src="" alt="Image preview" class="w-16 h-16 rounded-full ml-4 hidden">

            <!-- Ícone da imagem (será ocultado quando uma imagem for selecionada) -->
            <svg id="image-icon" class="size-12 text-gray-300 w-16 h-16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
              <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd" />
            </svg>

            <!-- Botão para disparar o input de arquivo -->
            <button type="button" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-50" onclick="document.getElementById('file-input').click()">
              Change
            </button>

            <!-- Input de arquivo (oculto) -->
            <input type="file" id="file-input" name="photo" accept="image/*" class="hidden" onchange="previewImage(event)">
          </div>
        </div>
      </div>
    </div>
    <button type="submit" class="text-gray-600 py-2 px-4 mt-4 rounded-lg bg-gray-100 hover:bg-cyan-400 hover:text-white transition">
      <i class="fas fa-plus mr-2"></i> Cadastrar usuário
    </button>
</div>

</form>
</div>
@endsection

<script>
  // Função para mostrar a pré-visualização da imagem
  function previewImage(event) {
    const reader = new FileReader();
    const imagePreview = document.getElementById('image-preview');
    const imageIcon = document.getElementById('image-icon');

    reader.onload = function() {
      imagePreview.src = reader.result;
      imagePreview.classList.remove('hidden'); // Torna a prévia visível
      imageIcon.classList.add('hidden'); // Oculta o ícone
    }

    if (event.target.files[0]) {
      reader.readAsDataURL(event.target.files[0]);
    }
  }
</script>