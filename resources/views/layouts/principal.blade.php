<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Principal</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <style>
    [x-cloak] {
      display: none !important;
    }

    .rotate-180 {
      transform: rotate(180deg);
    }

    .transition-transform {
      transition: transform 0.3s ease;
    }
  </style>
</head>

<body class="bg-white h-screen flex flex-col">
  <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>
  <nav class="bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <!-- Mobile menu button -->
        <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
          <button id="toggleSidebar" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 ml-2">
            <span class="sr-only">Abrir menu</span>
            <!-- Hamburger Icon -->
            <svg class="h-6 w-6 block hamburger-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <!-- Close Icon -->
            <svg class="h-6 w-6 hidden close-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Logo e Search -->
        <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
          <div class="flex shrink-0 items-center ml-6">
            <img class="h-8 w-auto" src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500" alt="Logo">
          </div>
          <div class="hidden sm:ml-6 sm:block">
            @include('layouts.search')
          </div>
        </div>

        <!-- Notificações e Perfil -->
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
          <!-- ... (mantenha o conteúdo original do perfil) ... -->
        </div>
      </div>
    </div>
  </nav>

  @include('componentes.toast')

  <div class="flex-1 flex overflow-hidden">
    <!-- Sidebar -->
    <div class="bg-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out h-full border-r z-20" id="sidebar">
      <nav>
        @auth
        @foreach($menus as $menu)
        @if($menu->children->count())
        <div x-data="{ open: false }" class="relative">
          <button @click="open = !open" class="w-full flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100">
            <i class="{{ $menu->icon }}"></i>
            <span class="ml-1">{{ $menu->name }}</span>
            <i class="fas fa-chevron-down ml-auto text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
          </button>

          <div x-show="open" class="ml-4">
            @foreach($menu->children as $child)
            @if(auth()->user()->hasPermission($child->slug, 'view_post'))
            <a href="{{ route($child->route) }}" class="flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100 {{ request()->routeIs($child->route) ? 'bg-cyan-50 text-cyan-600' : '' }}">
              <i class="{{ $child->icon }}"></i>
              <span class="ml-1">{{ $child->name }}</span>
            </a>
            @endif
            @endforeach
          </div>
        </div>
        @else
        @if(auth()->user()->hasPermission($menu->slug, 'view_post'))
        <a href="{{ route($menu->route) }}" class="flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100 {{ request()->routeIs($menu->route) ? 'bg-cyan-50 text-cyan-600' : '' }}">
          <i class="{{ $menu->icon }}"></i>
          <span class="ml-1">{{ $menu->name }}</span>
        </a>
        @endif
        @endif
        @endforeach
        @endauth
      </nav>

      <!-- Logout -->
      <form action="/logout" method="POST" class="absolute bottom-0 w-full left-0 px-2">
        @csrf
        <button type="submit" class="w-full flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100">
          <i class="fas fa-sign-out-alt"></i>
          <span class="ml-1">Sair</span>
        </button>
      </form>
    </div>

    <!-- Conteúdo Principal -->
    <div class="flex-1 p-4 overflow-auto">
      @yield('conteudo')
    </div>
  </div>

  @stack('scripts')
</body>
<script>
  const toggleButton = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');
  const hamburgerIcon = document.querySelector('.hamburger-icon');
  const closeIcon = document.querySelector('.close-icon');

  // Alternar menu
  toggleButton.addEventListener('click', () => {
    if (window.innerWidth >= 768) return; // Evita toggle no desktop

    const isOpen = !sidebar.classList.contains('-translate-x-full');
    sidebar.classList.toggle('-translate-x-full');
    hamburgerIcon.classList.toggle('hidden', !isOpen);
    closeIcon.classList.toggle('hidden', isOpen);
  });

  // Clicar em item do menu (fechar no mobile)
  document.querySelectorAll('#sidebar nav a').forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth < 768) {
        sidebar.classList.add('-translate-x-full');
        hamburgerIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
      }
    });
  });

  // Clicar fora do menu (fechar no mobile)
  document.addEventListener('click', (e) => {
    if (
      !sidebar.contains(e.target) &&
      !toggleButton.contains(e.target) &&
      window.innerWidth < 768
    ) {
      sidebar.classList.add('-translate-x-full');
      hamburgerIcon.classList.remove('hidden');
      closeIcon.classList.add('hidden');
    }
  });

  // Responsivo: atualizar ícones no resize
  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
      sidebar.classList.remove('-translate-x-full');
      hamburgerIcon.classList.remove('hidden');
      closeIcon.classList.add('hidden');
    } else {
      sidebar.classList.add('-translate-x-full');
      hamburgerIcon.classList.remove('hidden');
      closeIcon.classList.add('hidden');
    }
  });

  // Inicialização no load
  window.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth < 768) {
      sidebar.classList.add('-translate-x-full');
      hamburgerIcon.classList.remove('hidden');
      closeIcon.classList.add('hidden');
    }
  });
</script>


</html>