<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Principal</title>
  <script>
    (function () {
      try {
        var KEY = 'controle-estoque:theme';
        var stored = localStorage.getItem(KEY);
        var preferred = (stored === 'dark' || stored === 'light')
          ? stored
          : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        var isDark = preferred === 'dark';
        document.documentElement.classList.toggle('dark', isDark);
        if (document.body) {
          document.body.classList.toggle('dark', isDark);
          document.body.dataset.theme = preferred;
        } else {
          document.addEventListener('DOMContentLoaded', function(){
            document.body.classList.toggle('dark', isDark);
            document.body.dataset.theme = preferred;
          }, { once: true });
        }
      } catch (_) { }
    })();
  </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script> -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <!-- Configura Tailwind CDN para usar dark mode por classe -->
  <script>try{window.tailwind=window.tailwind||{}; tailwind.config={ darkMode: 'class' };}catch(e){}</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
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

    /* Campos mais suaves no claro/escuro */
    input[type="text"],
    input[type="number"],
    input[type="date"],
    input[type="email"],
    input[type="password"],
    select,
    textarea { background-color: #f8fafc; color: #0f172a; border-color: #e5e7eb; }

    .dark input[type="text"],
    .dark input[type="number"],
    .dark input[type="date"],
    .dark input[type="email"],
    .dark input[type="password"],
    .dark select,
    .dark textarea { background-color: #0b1220; color: #e5e7eb; border-color: #334155; }
    .dark ::placeholder { color: #9ca3af; }

    /* Select2 no escuro */
    .dark .select2-container .select2-selection--single,
    .dark .select2-container--default .select2-selection--multiple { background-color: #0b1220 !important; border-color: #334155 !important; color: #e5e7eb !important; }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #e5e7eb !important; }
    .dark .select2-dropdown { background-color: #0b1220 !important; color: #e5e7eb !important; border-color: #334155 !important; }
    .dark .select2-results__option--highlighted { background-color: #1f2937 !important; color: #e5e7eb !important; }

    /* DataTables no escuro */
    .dark .dataTables_wrapper .dataTables_filter input,
    .dark .dataTables_wrapper .dataTables_length select,
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button { background-color: #0b1220 !important; color: #e5e7eb !important; border: 1px solid #334155 !important; }
    .dark table.dataTable thead th { border-bottom: 1px solid #334155 !important; }
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current { background-color: #0891b2 !important; color: #fff !important; border-color: #0891b2 !important; }

  </style>
</head>

<body class="bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-100 h-screen flex flex-col">
  <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>
  <nav class="bg-white shadow-sm dark:bg-slate-900 dark:shadow-slate-900/40">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <!-- Mobile menu button -->
        <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
          <button id="toggleSidebar" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 ml-2 dark:text-gray-300 dark:hover:text-white dark:hover:bg-slate-800">
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

  <!-- Botão fixo para alternar tema (visível em todas as páginas Blade) -->
  <button id="themeToggle" type="button" aria-pressed="false" title="Alternar tema"
    class="fixed right-3 top-20 md:top-4 z-40 inline-flex items-center justify-center rounded-full p-2 text-gray-600 transition-colors duration-200 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:text-gray-300 dark:hover:bg-slate-700 dark:focus:ring-offset-slate-900">
    <span class="sr-only">Alternar tema</span>
    <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364-6.364-1.06 1.06M6.697 17.303l-1.06 1.06m12.727 0-1.06-1.06M6.697 6.697l-1.06-1.06M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z" /></svg>
    <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7.5 7.5 0 009.79 9.79z" /></svg>
  </button>

  @include('componentes.toast')

  <div class="flex-1 flex overflow-hidden">
    <!-- Sidebar -->
    <div class="bg-white dark:bg-slate-900 dark:border-slate-800 w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out h-full border-r z-20" id="sidebar">
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
            <a href="{{ route($child->route) }}" class="flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800 {{ request()->routeIs($child->route) ? 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-200' : '' }}">
              <i class="{{ $child->icon }}"></i>
              <span class="ml-1">{{ $child->name }}</span>
            </a>
            @endif
            @endforeach
          </div>
        </div>
        @else
        @if(auth()->user()->hasPermission($menu->slug, 'view_post'))
        <a href="{{ route($menu->route) }}" class="flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800 {{ request()->routeIs($menu->route) ? 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-200' : '' }}">
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
        <button type="submit" class="w-full flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-slate-800">
          <i class="fas fa-sign-out-alt"></i>
          <span class="ml-1">Sair</span>
        </button>
      </form>
    </div>

    <!-- Conteúdo Principal -->
    <div class="flex-1 p-4 overflow-auto bg-gray-50 dark:bg-slate-950 transition-colors">
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
  const themeToggle = document.getElementById('themeToggle');
  const iconSun = document.getElementById('iconSun');
  const iconMoon = document.getElementById('iconMoon');

  // Tema: sincroniza ícones e estado do botão
  function syncThemeButton() {
    const isDark = document.documentElement.classList.contains('dark');
    if (themeToggle) themeToggle.setAttribute('aria-pressed', String(isDark));
    if (isDark) {
      if (iconSun) iconSun.classList.remove('hidden');
      if (iconMoon) iconMoon.classList.add('hidden');
    } else {
      if (iconSun) iconSun.classList.add('hidden');
      if (iconMoon) iconMoon.classList.remove('hidden');
    }
  }

  function setTheme(mode) {
    const isDark = mode === 'dark';
    document.documentElement.classList.toggle('dark', isDark);
    if (document.body) {
      document.body.classList.toggle('dark', isDark);
      document.body.dataset.theme = isDark ? 'dark' : 'light';
    }
    try { localStorage.setItem('controle-estoque:theme', isDark ? 'dark' : 'light'); } catch(_) {}
    syncThemeButton();
  }

  themeToggle && themeToggle.addEventListener('click', function () {
    const isDark = document.documentElement.classList.contains('dark');
    setTheme(isDark ? 'light' : 'dark');
  });

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
    syncThemeButton();
  });
</script>


</html>
