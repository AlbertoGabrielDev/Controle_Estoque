<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Principal</title>
    <!-- <link rel="stylesheet" href="/dist/output.css"> -->
     <!-- <link rel="stylesheet" href="{{asset('js/app.js')}}">  -->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
<div class="flex flex-col h-screen bg-gray-100">
  <div class="bg-white text-white shadow w-full p-2 flex items-center justify-between">
    <div class="flex items-center">
      <div class="hidden md:flex items-center">
        <img src="https://www.emprenderconactitud.com/img/POC%20WCS%20(1).png" alt="Logo" class="w-28 h-18 mr-2">
      </div>
      <div class="md:hidden flex items-center"> <!-- Se muestra solo en dispositivos pequeños -->
        <button id="menuBtn">
          <i class="fas fa-bars text-gray-500 text-lg"></i> <!-- Ícono de menú -->
        </button>
      </div>
  </div>

    <div class="space-x-5">
        <button>
            <i class="fas fa-bell text-gray-500 text-lg"></i>
        </button>

        <button>
            <i class="fas fa-user text-gray-500 text-lg"></i>
        </button>
    </div>
  </div>
  <div class="flex-1 flex">
    <div  class="p-2 bg-white w-60 flex flex-col hidden md:flex" id="sideNav">
      <nav class="list-group list-group-horizontal">
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white"href="{{route('categoria.inicio')}}">
          <i class="fas fa-home mr-2"></i>Inicio
        </a>
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('produtos.index')}}">
          <i class="fas fa-file-alt mr-2"></i>Produtos
        </a>
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('fornecedor.index')}}">
          <i class="fas fa-users mr-2"></i>Fornecedores
        </a>
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('marca.index')}}">
          <i class="fas fa-users mr-2"></i>Marca
        </a>
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('estoque.index')}}">
          <i class="fas fa-users mr-2"></i>Estoque
        </a>
        @can('permissao')
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('usuario.index')}}">
          <i class="fas fa-users mr-2"></i>Usuarios
        </a>
        @endcan
        @can('permissao')
        <a class="block text-gray-500 py-2.5 px-4 my-4 rounded transition duration-200 hover:bg-gradient-to-r hover:from-cyan-400 hover:to-cyan-300 hover:text-white" href="{{route('estoque.historico')}}">
          <i class="fas fa-users mr-2"></i>Historico
        </a>
        @endcan
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
@if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="alert alert-danger" id="error-message" style="display: none;">
 EROUUUU!!!!
</div>
</body>
</html>