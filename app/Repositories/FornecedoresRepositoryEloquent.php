<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FornecedoresRepository;
use App\Models\Fornecedor;
use App\Validators\FornecedoresValidator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Telefone;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ValidacaoFornecedor;

/**
 * Class FornecedoresRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FornecedoresRepositoryEloquent extends BaseRepository implements FornecedoresRepository
{

    protected $fieldSearchable = [
        'email' => 'like',
        'nome_fornecedor' => 'like',
        'cnpj' => 'like',
        'cep' => 'like',
        'logradouro' => 'like',
        'bairro' => 'like',
        'cidade' => 'like',
        'UF' => 'like',
    ];

    public function model()
    {
        return Fornecedor::class;
    }
    

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function index()
    {
        if (Gate::allows('permissao')) {
            $query = Fornecedor::query();
            $this->applyLikeConditions($query, request()->get('searchLike'));
           return $query->paginate(15);
        }

        return Fornecedor::where('status', 1)->paginate(15);
    }

    public function inserirCadastro(ValidacaoFornecedor $request)
    {
        $fornecedor = Fornecedor::create([
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
        $telefones = Telefone::create([
            'ddd' => $request->ddd,
            'telefone' => $request->telefone,
            'principal' => $request->input('principal') ? $request->principal : 0,
            'whatsapp' => $request->input('whatsapp') ? $request->whatsapp : 0,
            'telegram' => $request->input('telegram') ? $request->telegram : 0,
            'id_fornecedor_fk' => $fornecedorId->id_fornecedor
        ]);
   
    }

    public function editar( $fornecedorId){
        $fornecedores = Fornecedor::where('fornecedor.id_fornecedor',$fornecedorId)->get();
        $telefones = Fornecedor::find($fornecedorId)->telefones;
        return compact('fornecedores','telefones');
    }

    public function salvarEditar(Request $request, $fornecedorId) {
        Telefone::where('id_fornecedor_fk', $fornecedorId)
        ->update([
            'ddd' => $request->ddd,
            'telefone' => $request->telefone,
            'principal' => $request->input('principal') ? $request->principal : 0,
            'whatsapp' => $request->input('whatsapp') ? $request->whatsapp : 0,
            'telegram' => $request->input('telegram') ? $request->telegram : 0
        ]);
        
       
    }

    public function status(Request $request, $statusId)
    {
        $status = Fornecedor::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }
    
}
