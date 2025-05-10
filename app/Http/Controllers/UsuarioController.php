<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Http\Requests\ValidacaoUsuario;
use App\Models\Role;
use App\Models\Unidades;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function Index()
    {
        $usuarios = User::select([
            'users.id',
            'users.name',
            'users.email',
            'users.created_at',
            'users.profile_photo_path',
            'users.status',
            DB::raw("GROUP_CONCAT(roles.name ORDER BY roles.name ASC SEPARATOR ', ') as role_names")
        ])
            ->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')
            ->leftJoin('roles', 'user_role.role_id', '=', 'roles.id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.profile_photo_path', 'users.status')
            ->paginate(10);

        return view('usuario.index', compact('usuarios'));
    }

    public function Cadastro()
    {
        $roles = Role::all();
        $units = Unidades::all();
        return view('usuario.cadastro', compact('units', 'roles'))->with('success', 'Usuario inserido com sucesso');
    }

    public function buscar(Request $request)
    {
        $usuarios = User::where('name', 'like', '%' . $request->input('name') . '%')
            ->where('id', '!=', 1)
            ->paginate(10);
        return view('usuario.index', compact('usuarios'));
    }

    public function editar($usuarioId)
    {
        $usuario = User::with(['unidade', 'roles'])->findOrFail($usuarioId);
        $units = Unidades::all();
        $roles = Role::all();

        return view('usuario.editar', compact('usuario', 'units', 'roles'));
    }
    public function salvarEditar(Request $request, $userId)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id'
        ]);

        $usuario = User::find($userId);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $requestImage = $request->photo;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestImage->move(public_path('img/usuario'), $imageName);
        }

        $updateData = [
            'name'  => $request->name,
            'email' => $request->email,
            'id_unidade_fk' => $request->id_unidade,
            'profile_photo_path' => isset($imageName) ? $imageName : $usuario->profile_photo_path,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $usuario->update($updateData);

        $usuario->roles()->sync($request->roles);

        return redirect()->route('usuario.index')->with('success', 'Editado com sucesso');
    }

    public function status($statusId)
    {
        $status = User::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json([
            'status' => 200,
            'message' => 'Status atualizado com sucesso!',
            'type' => 'success'
        ]);
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
        try {
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $requestImage = $request->photo;
                $extension = $requestImage->extension();
                $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
                $requestImage->move(public_path('img/usuario'), $imageName);
            }

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'id_unidade_fk' => $request->id_unidade,
                'profile_photo_path' => isset($imageName) ? $imageName : null,
            ]);

            $usuario = User::where('email', $request->email)->first();
            $usuario->roles()->sync($request->roles);

            return redirect()->route('usuario.index')->with('success', 'Inserido com sucesso');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erro ao inserir usuário: ' . $e->getMessage()]);
        }
    }
}
