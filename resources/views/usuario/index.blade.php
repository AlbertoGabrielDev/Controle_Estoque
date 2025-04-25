@extends('layouts.principal')

@section('conteudo')

  <h5 class="text-center text-2xl font-semibold text-gray-700 mb-6">Authors Table</h5>
  <div class="flex justify-between mb-4">
    <a class="text-gray-600 py-2 px-4 rounded-lg bg-gray-100 hover:bg-cyan-400 hover:text-white transition" href="{{route('categoria.inicio')}}">
      <i class="fa fa-angle-left mr-2"></i>Voltar
    </a>
    <a class="text-gray-600 py-2 px-4 rounded-lg bg-gray-100 hover:bg-cyan-400 hover:text-white transition" href="{{route('usuario.cadastro')}}">
      <i class="fas fa-plus mr-2"></i>Cadastrar
    </a>
  </div>

  <table class="table-auto w-full border-collapse border border-gray-200 rounded-md ">
    <thead class="bg-gray-100">
      <tr class="text-sm text-gray-600">
        <th class="py-3 px-6 text-left font-medium">Usuario</th>
        <th class="py-3 px-6 text-left font-medium">Permissões</th>
        <th class="py-3 px-6 text-left font-medium">Status</th>
        <th class="py-3 px-6 text-left font-medium">Ativo desde</th>
        <th class="py-3 px-6 text-left font-medium">Editar</th>
        <th class="py-3 px-6 text-left font-medium">Ativar/Inativar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($usuarios as $usuario)
      <tr class="border-b border-gray-200 hover:bg-gray-50">
        <td class="py-4 px-6 flex items-center">
          <img src="{{ $usuario->profile_photo_path ? '/img/usuario/'.$usuario->profile_photo_path : '/img/default-avatar.png' }}"
            alt="{{$usuario->name}}"
            class="w-10 h-10 mr-4 rounded-lg">
          <div>
            <p class="text-gray-800 font-semibold">{{$usuario->name}}</p>
            <p class="text-gray-500 text-sm">{{$usuario->email}}</p>
          </div>
        </td>
        <td class="py-4 px-6 text-gray-600">
          @php
          $roles = explode(', ', $usuario->role_names);
          @endphp
          <div>
            @foreach ($roles as $index => $role)
            @if ($index > 0 && $index % 2 == 0)
            <br>
            @endif
            {{ ucfirst($role) }}@if($index < count($roles) - 1),@endif
              @endforeach
              </div>
        </td>
        <td class="py-4 px-6">
          <span class="px-3 py-1 text-sm rounded-full {{ $usuario->status ? 'bg-green-200 text-green-700' : 'bg-gray-200 text-gray-700' }}">
            {{ $usuario->status ? 'Online' : 'Offline' }}
          </span>
        </td>
        <td class="py-4 px-6 text-gray-600">{{ \Carbon\Carbon::parse($usuario->created_at)->format('d/m/Y') }}</td>
        <td class="py-4 px-6">
          <a href="{{route('usuario.editar', $usuario->id)}}" class="text-cyan-600 hover:underline">Editar</a>
        </td>
        <td class="py-4 px-6">
          <button class="toggle-ativacao @if($usuario->status === 1) btn-danger @elseif($usuario->status === 0) btn-success @else btn-primary @endif" data-id="{{ $usuario->id}}">
            {{ $usuario->status ? 'Inativar' : 'Ativar' }}
          </button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-4 flex justify-center">
    <nav class="flex items-center space-x-2">
      <a href="{{$usuarios->previousPageUrl()}}" class="py-2 px-3 bg-gray-100 border rounded-l-lg hover:bg-gray-200 text-gray-600">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
        </svg>
      </a>
      <span class="py-2 px-3 bg-gray-100 text-gray-600">{{$usuarios->currentPage()}}</span>
      <a href="{{$usuarios->nextPageUrl()}}" class="py-2 px-3 bg-gray-100 border rounded-r-lg hover:bg-gray-200 text-gray-600">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
      </a>
    </nav>
  </div>

@endsection