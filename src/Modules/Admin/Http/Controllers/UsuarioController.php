<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Unidades;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Admin\Http\Requests\UsuarioStoreRequest;
use Modules\Admin\Http\Requests\UsuarioUpdateRequest;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Admin\Services\UsuarioService;

class UsuarioController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private UsuarioService $usuarios
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
                $dt->addColumn('avatar', fn ($row) => $this->usuarios->resolveAvatarPath($row->avatar ?? null));
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

    public function salvarEditar(UsuarioUpdateRequest $request, $userId)
    {
        $usuario = User::query()->findOrFail($userId);
        $this->usuarios->update($usuario, $request->validated(), $request->file('photo'));

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

    public function inserirUsuario(UsuarioStoreRequest $request)
    {
        $this->usuarios->create($request->validated(), $request->file('photo'));

        return redirect()->route('usuario.index')->with('success', 'Inserido com sucesso');
    }
}
