<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FornecedorRepository;
use App\Models\Fornecedor;
use App\Models\Telefone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class FornecedorRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FornecedorRepositoryEloquent extends BaseRepository implements FornecedorRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Fornecedor::class;
    }

    public function index(){
        $fornecedores = Gate::allows('permissao') ? Fornecedor::paginate(15) : Fornecedor::where('status',1)->paginate(15);
        return response()->json($fornecedores);
    }

    public function inserirFornecedor(Request $request){
        Fornecedor::create([
            'nome_fornecedor'   =>$request->nome_fornecedor,
            'cnpj'              =>$request->cnpj,
            'cep'               =>$request->cep,
            'logradouro'        =>$request->logradouro,
            'bairro'            =>$request->bairro,
            'numero_casa'       =>$request->numero_casa,
            'email'             =>$request->email,
            'id_users_fk'       =>Auth::id(),
            'cidade'            =>$request->cidade,
            'uf'                =>$request->uf
       ]);
       $fornecedorId = Fornecedor::latest('id_fornecedor')->first();
        Telefone::create([
            'ddd' => $request->ddd,
            'telefone' => $request->telefone,
            'principal' => $request->input('principal') ? $request->principal : 0,
            'whatsapp' => $request->input('whatsapp') ? $request->whatsapp : 0,
            'telegram' => $request->input('telegram') ? $request->telegram : 0,
            'id_fornecedor_fk' => $fornecedorId->id_fornecedor
        ]);
       
    }

    public function salvarEditar(Request $request, $fornecedorId) {
        Telefone::where('id_fornecedor_fk', $fornecedorId)
        ->update([
            'ddd'           => $request->ddd,
            'telefone'      => $request->telefone,
            'principal'     => $request->input('principal') ? $request->principal : 0,
            'whatsapp'      => $request->input('whatsapp') ? $request->whatsapp : 0,
            'telegram'      => $request->input('telegram') ? $request->telegram : 0,
            
        ]);
    }
    
    public function  buscar(Request $request) {
        if (Gate::allows('permissao')) {
            $fornecedores = Fornecedor::where('nome_fornecedor', 'like' , '%' . $request->input('nome_fornecedor'). '%')->paginate(15);
        } else {
            $fornecedores = Fornecedor::where('nome_fornecedopr', 'like' , '%' . $request->input('nome_fornecedor'). '%')->where('status',1)->paginate(15);
        }
        return $fornecedores;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
