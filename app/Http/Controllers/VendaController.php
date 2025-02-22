<?php

namespace App\Http\Controllers;

use App\Events\VendaRegistrada;
use App\Models\Produto;
use Illuminate\Http\Request;

class VendaController extends Controller
{

    public function vendas() {
        return view('vendas.venda');
    }
    public function registrar(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);
        
        $produto = Produto::where('qrcode', $request->codigo)->firstOrFail();
        
        $produto->decrement('estoque');
        
        broadcast(new VendaRegistrada($produto));
        
        return response()->json(['success' => true]);
    }
}
