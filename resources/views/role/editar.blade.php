@extends('layouts.principal')

@section('conteudo')


    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h3 class="font-semibold mb-3">{{ ucfirst($role->name) }}</h3>
        <div class="border p-4 rounded-lg mb-6">  
            <div class="grid grid-cols-4 gap-4">
                <div class="flex flex-col items-center">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="hidden" checked>
                        <div class="toggle-switch"></div>
                    </label>
                    <span class="text-sm text-gray-600">Dashboard</span>
                </div>
                <div class="flex flex-col items-center">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="hidden">
                        <div class="toggle-switch"></div>
                    </label>
                    <span class="text-sm text-gray-600">Reports</span>
                </div>
                <div class="flex flex-col items-center">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="hidden" checked>
                        <div class="toggle-switch"></div>
                    </label>
                    <span class="text-sm text-gray-600">Custom Apps</span>
                </div>
                <div class="flex flex-col items-center">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="hidden">
                        <div class="toggle-switch"></div>
                    </label>
                    <span class="text-sm text-gray-600">API Access</span>
                </div>
            </div>
        </div>

        <!-- Assign Permissions -->
        <div class="border rounded-lg p-4">
            <h3 class="font-semibold mb-3">Assign Permissions</h3>
            <form method="POST" action="{{ route('roles.salvarEditar', $role->id) }}">
                @csrf
                @method('PUT')

                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2 w-1/6">Menu</th>
                            @foreach ($permissions as $permission)
                            <th class="border border-gray-300 p-2">{{ $permission->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menus as $menu)
                        <tr>
                            <td class="border border-gray-300 p-2 font-medium">{{ $menu->name }}</td>
                            @foreach ($permissions as $permission)
                            <td class="border border-gray-300 p-2 text-center">
                                <input type="checkbox"
                                    class="permission-checkbox"
                                    name="permissions[{{ $menu->id }}][]"
                                    value="{{ $permission->id }}"
                                    {{ $role->hasMenuPermission($menu->id, $permission->id) ? 'checked' : '' }}>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Salvar Permissões
                    </button>
                </div>
            </form>
        </div>
    </div>


@endsection
<style>
    .toggle-switch {
        width: 40px;
        height: 20px;
        background: #ccc;
        border-radius: 9999px;
        position: relative;
        cursor: pointer;
        transition: background 0.3s;
    }

    .toggle-switch::after {
        content: "";
        width: 18px;
        height: 18px;
        background: white;
        position: absolute;
        top: 1px;
        left: 2px;
        border-radius: 50%;
        transition: transform 0.3s;
    }

    input:checked+.toggle-switch {
        background: #3b82f6;
    }

    input:checked+.toggle-switch::after {
        transform: translateX(20px);
    }

    .permission-checkbox {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #ccc;
        appearance: none;
        cursor: pointer;
        transition: background 0.3s, border-color 0.3s;
    }

    .permission-checkbox:checked {
        background: #10b981;
        border-color: #10b981;
    }
</style>

<script>
    function toggleSwitch(element) {
        let checkbox = element.previousElementSibling;
        checkbox.checked = !checkbox.checked;
    }
</script>
</body>

</html>