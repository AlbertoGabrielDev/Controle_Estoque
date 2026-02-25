<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Admin\Http\Requests\RolePermissionsRequest;
use Modules\Admin\Http\Requests\RoleStoreRequest;
use Modules\Admin\Models\Role;
use Modules\Admin\Services\RoleService;

class RoleController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private RoleService $roles
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Roles/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Role::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('roles.editar', $row->id),
                    ]);
                });
            }
        );
    }

    public function cadastro()
    {
        return Inertia::render('Roles/Create');
    }

    public function buscar(Request $request)
    {
        return redirect()->route('roles.index', [
            'q' => (string) $request->input('name', ''),
        ]);
    }

    public function inserirRole(RoleStoreRequest $request)
    {
        $this->roles->create($request->validated());

        return redirect()->route('roles.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($id)
    {
        $role = Role::query()->findOrFail($id, ['id', 'name']);

        return Inertia::render('Roles/Edit', $this->roles->buildEditPayload($role));
    }

    public function salvarEditar(RolePermissionsRequest $request, $roleId)
    {
        $role = Role::query()->findOrFail($roleId);
        $this->roles->updatePermissions($role, $request->validated());

        return redirect()->route('roles.editar', $role->id)->with('success', 'Permissoes atualizadas com sucesso!');
    }

    public function updateStatus($model, $id)
    {
        return response()->json([
            'status' => 422,
            'message' => 'Role nao possui status para alternancia.',
            'type' => 'warning',
        ], 422);
    }
}
