<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ValidacaoMarca;
use App\Repositories\MarcaRepository;

class MarcaController extends Controller
{
    protected $marcaRepository;

    public function __construct(MarcaRepository $marcaRepository)
    {
        $this->marcaRepository = $marcaRepository;
    }

    public function index()
    {
        $marcas = $this->marcaRepository->index();
        return view('marca.index', compact('marcas'));
    }

    public function cadastro()
    {   
        return view('marca.cadastro');
    }


    public function editar($marcaId)
    {
        $marcas = $this->marcaRepository->editar($marcaId);
        return view('marca.editar',compact('marcas'));
    }

    public function salvarEditar(ValidacaoMarca $request, $marcaId)
    {
        $this->marcaRepository->salvarEditar($request,$marcaId);
        return redirect()->route('marca.index')->with('success', 'Editado com sucesso');
    }

    public function inserirMarca(ValidacaoMarca $request)
    {
        $this->marcaRepository->inserirMarca($request);
        return redirect()->route('marca.index')->with('success', 'Inserido com sucesso');
    }

    public function status($statusId)
    {
        $status = Marca::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }

}
