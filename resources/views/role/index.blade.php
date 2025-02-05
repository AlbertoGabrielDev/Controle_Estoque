@extends('layouts.principal')

@section('conteudo')
<div class="bg-white p-6 rounded-md shadow-md w-full">
    <h5 class="text-center text-2xl font-semibold text-gray-700 mb-6">Roles Table</h5>
    <div class="flex justify-between mb-4">
        <a class="text-gray-600 py-2 px-4 rounded-lg bg-gray-100 hover:bg-cyan-400 hover:text-white transition" href="{{route('categoria.inicio')}}">
            <i class="fa fa-angle-left mr-2"></i>Voltar
        </a>
        <a class="text-gray-600 py-2 px-4 rounded-lg bg-gray-100 hover:bg-cyan-400 hover:text-white transition" href="{{route('roles.cadastro')}}">
            <i class="fas fa-plus mr-2"></i>Cadastrar
        </a>
    </div>

    <table class="table-auto w-full border-collapse border border-gray-200 rounded-md shadow-sm">
        <thead class="bg-gray-100">
            <tr class="text-sm text-gray-600">
                <th class="py-3 px-6 text-left font-medium">Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-4 px-6 flex items-center">
                    <div>
                        <p class="text-gray-800 font-semibold">{{ucfirst($role->name)}}</p>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection