<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Principal</title>
    <link rel="stylesheet" href="{{asset('css/welcome.css')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
</head>
<body>
  {{-- 
    Cabeçalho
  --}}
    <div class="container d-flex flex-row justify-content-between align-items-center">
        <div class="mx-auto">
          <ul class="list-group list-group-horizontal">
            <a class="list-group-item fs-5" href="{{route('categoria.inicio')}}">Inicio</a>
            <a class="list-group-item fs-5" href="{{route('produtos.index')}}">Produtos</a>
            <a class="list-group-item fs-5" href="{{route('fornecedor.index')}}">Fornecedores</a>
            <a class="list-group-item fs-5" href="{{route('estoque.index')}}">Estoque</a>
            <a class="list-group-item fs-5" href="{{route('marca.index')}}">Marca</a>
            @can('permissao')
              <a class="list-group-item fs-5" href="{{route('usuario.index')}}">Usuarios</a>
            @endcan
          </ul>
        </div>
        <form action="/logout" method="POST">
          @csrf
          <a href="/logout" class="list-group-item" onclick="event.preventDefault();
            this.closest('form').submit();">Sair</a>
        </form>
      </div>
{{-- 
    Cabeçalho
  --}}      
  {{-- 
    -------------------------------------------------------------
    Corpo
  --}}
@if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif

@yield('conteudo')  
{{-- 
    Corpo
  --}}      
</body>
</html>