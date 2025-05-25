<?php

namespace App\Http\Controllers;

use App\Repositories\UnidadesRepository;
use Illuminate\Http\Request;

class UnidadeController extends Controller
{
    protected $unidadeRepository;
    public function __construct(UnidadesRepository $unidadeRepository)
    {
        $this->unidadeRepository = $unidadeRepository;
    }
    public function index()
    {
        $unidades = $this->unidadeRepository->index();
        return view('unidades.index',compact('unidades'));
        
    }
    public function cadastro()
    {
        return view('unidades.cadastro');
    }

    public function inserirUnidade(Request $request)
    {
        $this->unidadeRepository->inserirUnidade($request);
        return redirect()->route('unidade.index')->with('success', 'Inserido com sucesso');
    }
    public function editar($unidadeId)
    {
        $unidades = $this->unidadeRepository->editar($unidadeId);
        return view('unidades.editar', compact('unidades'));
    }

    public function salvarEditar(Request $request,$unidadeId)
    {
        $this->unidadeRepository->salvarEditar($request, $unidadeId);
        return redirect()->route('unidade.index')->with('success', 'Editado com sucesso');
    }

    public function buscar(Request $request)
    {
        $unidades = $this->unidadeRepository->buscar($request);
        return view('unidades.index', compact('unidades'));
    }

    public function status($statusId)
    {   
        $status = $this->unidadeRepository->status($statusId);
        return response()->json(['status' => $status->status]);
    }
}
