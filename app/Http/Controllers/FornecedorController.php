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
use App\Repositories\FornecedoresRepository;

class FornecedorController extends Controller
{
    protected $fornecedorRepository;

    public function __construct(FornecedoresRepository $fornecedorRepository)
    {
        $this->fornecedorRepository = $fornecedorRepository;
    }

    public function index()
    {
        $fornecedores = $this->fornecedorRepository->index();
        return view('fornecedor.index', compact('fornecedores'));
    }

    public function cadastro()
    {
        return view('fornecedor.cadastro');
    }

    public function inserirCadastro(ValidacaoFornecedor $request)
    {
        $this->fornecedorRepository->inserirEstoque($request);
        return redirect()->route('fornecedor.index')->with('success','Inserido com sucesso');
    }

    public function editar( $fornecedorId){
        $fornecedores = $this->fornecedorRepository->editar( $fornecedorId);
        return view('fornecedor.editar', $fornecedores);
    }

    public function salvarEditar(Request $request, $fornecedorId) {
        $this->fornecedorRepository->salvarEditar( $request,$fornecedorId);
        return redirect()->route('fornecedor.index')->with('success', 'Editado com sucesso');
    }

    public function status(Request $request, $statusId)
    {
        $status = Fornecedor::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }
}
