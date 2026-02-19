<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Unidades;
use App\Models\User;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    public function __construct(
        private DataTableService $dt
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Users/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = User::makeDatatableQuery($request);

        $query->addSelect([
            'users.profile_photo_path as avatar',
            'users.created_at as created_at',
            DB::raw("(SELECT GROUP_CONCAT(roles.name ORDER BY roles.name ASC SEPARATOR ', ') FROM user_roles LEFT JOIN roles ON roles.id = user_roles.role_id WHERE user_roles.user_id = users.id) as roles"),
        ]);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('roles', fn ($row) => trim((string) ($row->roles ?? '')) ?: '-');
                $dt->addColumn('avatar', fn ($row) => $this->resolveAvatarPath($row->avatar ?? null));
                $dt->addColumn('created_at_fmt', function ($row) {
                    if (empty($row->created_at)) {
                        return '-';
                    }

                    return Carbon::parse($row->created_at)->format('d/m/Y');
                });
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('usuario.editar', $row->id),
                        DataTableActions::status('usuario.status', 'usuario', $row->id, (bool) $row->st),
                    ]);
                });
            }
        );
    }

    public function cadastro()
    {
        return Inertia::render('Users/Create', [
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'units' => Unidades::query()->orderBy('nome')->get(['id_unidade', 'nome']),
        ]);
    }

    public function buscar(Request $request)
    {
        return redirect()->route('usuario.index', [
            'q' => (string) $request->input('name', ''),
        ]);
    }

    public function editar($usuarioId)
    {
        $usuario = User::with(['unidade', 'roles'])->findOrFail($usuarioId);

        return Inertia::render('Users/Edit', [
            'usuario' => $usuario,
            'units' => Unidades::query()->orderBy('nome')->get(['id_unidade', 'nome']),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function salvarEditar(Request $request, $userId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'id_unidade' => 'required|integer|exists:unidades,id_unidade',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id',
            'password' => 'nullable|string|min:6',
            'photo' => 'nullable|image|max:4096',
        ]);

        $usuario = User::query()->findOrFail($userId);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $requestImage = $request->file('photo');
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now')) . '.' . $extension;
            $requestImage->move(public_path('img/usuario'), $imageName);
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'id_unidade_fk' => $validated['id_unidade'],
            'profile_photo_path' => $imageName ?? $usuario->profile_photo_path,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $usuario->update($updateData);
        $usuario->roles()->sync($validated['roles']);

        return redirect()->route('usuario.index')->with('success', 'Editado com sucesso');
    }

    public function unidade()
    {
        $units = Unidades::all();
        return view('auth.login', compact('units'));
    }

    public function unidadeRegister()
    {
        $units = Unidades::all();
        return view('auth.register', compact('units'));
    }

    public function inserirUsuario(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'id_unidade' => 'required|integer|exists:unidades,id_unidade',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id',
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $requestImage = $request->file('photo');
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now')) . '.' . $extension;
            $requestImage->move(public_path('img/usuario'), $imageName);
        }

        $usuario = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'id_unidade_fk' => $validated['id_unidade'],
            'profile_photo_path' => $imageName ?? null,
        ]);

        $usuario->roles()->sync($validated['roles']);

        return redirect()->route('usuario.index')->with('success', 'Inserido com sucesso');
    }

    private function resolveAvatarPath(?string $avatar): string
    {
        if (!$avatar) {
            return asset('img/default-avatar.png');
        }

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://') || str_starts_with($avatar, '/')) {
            return $avatar;
        }

        return asset('img/usuario/' . ltrim($avatar, '/'));
    }
}
