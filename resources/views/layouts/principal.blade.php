<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Principal</title>
  <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- <link rel="stylesheet" href="/dist/output.css"> -->
  <!-- <link rel="stylesheet" href="{{asset('js/app.js')}}">  -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <!-- <script src="{{ asset('./resources/js/app.js') }}"></script> -->
</head>

<body>

  <nav class="bg-white">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
          <!-- Mobile menu button-->
          <button type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:outline-hidden focus:ring-inset" aria-controls="mobile-menu" aria-expanded="false">
            <span class="absolute -inset-0.5"></span>
            <span class="sr-only">Open main menu</span>

            <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>

            <svg class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start m-4">
          <div class="flex shrink-0 items-center">
            <img class="h-8 w-auto" src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">
          </div>
        </div>
        @include('layouts.search')

        <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
          <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
            <span class="absolute -inset-1.5"></span>
            <span class="sr-only">View notifications</span>
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
          </button>

          <!-- Profile dropdown -->
          <div class="relative ml-3">
            <div>
              <button type="button" id="user-menu-button" class="relative flex rounded-full bg-gray-800 text-sm focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                <span class="absolute -inset-1.5"></span>
                <span class="sr-only">Open user menu</span>
                <img class="size-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
              </button>
            </div>

            <div id="user-menu" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 ring-1 shadow-lg ring-black/5 hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
              <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Your Profile</a>
              <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Settings</a>
              <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">Sign out</a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </nav>
  @if(session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info') || $errors->any())
  @include('componentes.toast')
  @endif
  <div class="flex-1 flex">
    <div class="p-2 bg-white w-60 flex flex-col hidden md:flex" id="sideNav">
      <nav class="list-group list-group-horizontal">
   
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('months')}}">
          <i class="fas fa-history mr-2"></i>Grafico de vendas
        </a>
        
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('categoria.inicio')}}">
          <i class="fas fa-home mr-2"></i>Inicio
        </a>
        @if(auth()->user() && auth()->user()->hasPermission('produtos','view_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('produtos.index')}}">
          <i class="fas fa-file-alt mr-2"></i>Produtos
        </a>
        @endif
         @if(auth()->user() && auth()->user()->hasPermission('fornecedores','view_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('fornecedor.index')}}">
          <i class="fas fa-address-book mr-2"></i>Fornecedores
        </a>
        @endif
        @if(auth()->user() && auth()->user()->hasPermission('marcas','view_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('marca.index')}}">
          <i class="fas fa-hashtag mr-2"></i>Marca
        </a>
        @endif
        @if(auth()->user() && auth()->user()->hasPermission('estoque','view_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('estoque.index')}}">
          <i class="fas fa-server mr-2"></i>Estoque
        </a>
        @endif
        @if(auth()->user() && auth()->user()->hasPermission('perfil','view_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('usuario.index')}}">
          <i class="fas fa-users mr-2"></i>Usuarios
        </a>
        @endif
        @if(auth()->user() && auth()->user()->hasPermission('unidades','view_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('unidades.index')}}">
          <i class="fa-solid fa-suitcase"></i> Unidades
        </a>
        @endif
        @if(auth()->user() && auth()->user()->hasPermission('historico','view_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('estoque.historico')}}">
          <i class="fas fa-history mr-2"></i>Historico
        </a>
        @endif
        @if(auth()->user() && auth()->user()->hasPermission('roles','create_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('roles.index')}}">
          <i class="fa-solid fa-suitcase"></i> Permissões
        </a>
        @endif
        @if(auth()->user() && auth()->user()->hasPermission('historico_vendas','view_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('vendas.historico_vendas')}}">
          <i class="fas fa-users mr-2"></i>Historico de vendas
        </a>
        @endif
        @if(auth()->user() && auth()->user()->hasPermission('vendas','view_post'))
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('vendas.venda')}}">
          <i class="fas fa-users mr-2"></i>Vendas
        </a>
        @endif
      </nav>
      <form action="/logout" method="POST">
        @csrf
        <a class="block text-gray-500 py-2.5 px-4 my-2 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white mt-auto" onclick="event.preventDefault(); this.closest('form').submit();" href="/logout">
          <i class="fas fa-sign-out-alt mr-2"></i>Sair
        </a>
      </form>
    </div>
    @yield('conteudo')
  </div>
  </div>

  @stack('scripts')
</body>

</html>

<script>
  document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('user-menu');
    const userButton = document.getElementById('user-menu-button');

    if (!userButton.contains(event.target) && !userMenu.contains(event.target)) {
      userMenu.classList.add('hidden');
    }
  });

  $(document).ready(function() {
    $.fn.select2.defaults.set('language', 'pt-BR');

    $('.select2-multiple').select2({
      placeholder: "Selecione as permissões",
      allowClear: true,
      width: '100%'
    });
  });
</script>