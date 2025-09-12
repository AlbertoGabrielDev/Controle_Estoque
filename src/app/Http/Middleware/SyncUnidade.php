<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use App\Models\Unidades;

class SyncUnidade
{
    /**
     * Garante que a unidade atual esteja sempre na sessão e no cookie.
     * Também compartilha a unidade para views e disponibiliza no container.
     */
    public function handle(Request $request, Closure $next)
    {
        // Se veio da sessão, garante cookie (30 dias)
        if (session()->has('id_unidade')) {
            $id = session('id_unidade');
            Cookie::queue(cookie('id_unidade', $id, 60 * 24 * 30)); // 30 dias
        } else {
            // Se sessão não tem e cookie tem, restaura sessão
            $fromCookie = $request->cookie('id_unidade');
            if ($fromCookie) {
                session(['id_unidade' => $fromCookie]);
            }
        }

        // Carrega a unidade e compartilha (para blades e container)
        $unidade = null;
        if ($id = session('id_unidade')) {
            $unidade = Unidades::find($id);
        }

        // Disponível via app('current.unidade')
        app()->instance('current.unidade', $unidade);

        // Disponível em todos os blades como $unidadeAtual
        View::share('unidadeAtual', $unidade);

        return $next($request);
    }
}
