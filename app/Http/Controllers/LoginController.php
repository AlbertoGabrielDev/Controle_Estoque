<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

       
        $user = User::where('email', $request->email)->first();

        if ($user && $user->status === 0) {
            return redirect()->back()->withInput()->withErrors(['status' => 'Você não pode fazer login. Fale com o seu administrador']);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            
            return redirect()->intended('/verdurao/categoria');
        }
        return redirect()->back()->withInput()->withErrors(['email' => 'Credenciais inválidas.']);
    }
}
