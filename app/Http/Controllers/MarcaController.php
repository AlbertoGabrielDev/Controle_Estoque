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

    public function __construct(MarcaRepository $marcaRepository) {
        $this->marcaRepository = $marcaRepository;
    }

    public function index()
    {
        $marcas = $this->marcaRepository->index();
        return response()->json($marcas);
    }

    public function buscar(Request $request) 
    {
        $marcas = $this->marcaRepository->buscar($request);
        return response()->json($marcas);
    } 

    public function editar(Request $request, $marcaId)
    {
        $editar = $this->marcaRepository->editar($request, $marcaId);
        return response()->json([$editar], 200);
    }

    public function salvarEditar(ValidacaoMarca $request, $marcaId)
    {
        $editar = $this->marcaRepository->editar($request, $marcaId);
        return response()->json([$editar, 'message' => 'Editado com sucesso'], 200);
    }

    public function inserirMarca(ValidacaoMarca $request)
    {
        $inserir = $this->marcaRepository->cadastro($request);
        return response()->json([$inserir, 'message' => 'Inserido com sucesso'], 200);
    }

    public function status($statusId)
    {
      
        $status = $this->marcaRepository->status($statusId);
        return response()->json(['status' => $status->status]);
    }

}
