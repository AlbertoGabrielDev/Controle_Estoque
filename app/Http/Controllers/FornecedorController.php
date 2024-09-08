<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\Telefone;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ValidacaoFornecedor;
use App\Repositories\FornecedorRepository;

class FornecedorController extends Controller
{
    protected $fornecedorRepository;

    public function __construct(FornecedorRepository $fornecedorRepository)
    {
        $this->fornecedorRepository = $fornecedorRepository ;
    }

    public function index()
    {
        $fornecedor =$this->fornecedorRepository->index();
        return response()->json($fornecedor);
    }

    // public function cadastro()
    // {
    //     return view('fornecedor.cadastro');
    // }

    public function buscar(Request $request)
    {    
        $fornecedor =$this->fornecedorRepository->buscar($request);
        return response()->json($fornecedor);
    }

    public function inserirCadastro(ValidacaoFornecedor $request)
    {
        $fornecedor =$this->fornecedorRepository->inserirFornecedor($request);
        return response()->json($fornecedor);
    }

    // public function editar(Request $request, $fornecedorId){
    //     $fornecedores = Fornecedor::where('fornecedor.id_fornecedor',$fornecedorId)->get();
    //     $telefones = Fornecedor::find($fornecedorId)->telefones;
    //     return view('fornecedor.editar', compact('fornecedores','telefones'));
    // }

    public function salvarEditar(Request $request, $fornecedorId) {
        $fornecedor =$this->fornecedorRepository->salvarEditar($request, $fornecedorId);
        return response()->json($fornecedor);
    }

    public function status(Request $request, $statusId)
    {
        $status = Fornecedor::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }
}
