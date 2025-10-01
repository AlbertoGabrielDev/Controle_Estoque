<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tipo_pessoa' => 'required|in:PF,PJ',
            'documento'   => 'nullable|string|max:20',
            'razao_social'=> 'nullable|string|max:120',
            'nome_fantasia'=> 'nullable|string|max:120',
            'nome'        => 'nullable|string|max:120',
            'email'       => 'nullable|email|max:150',
            'whatsapp'    => 'nullable|string|max:30',
            'telefone'    => 'nullable|string|max:30',
            'site'        => 'nullable|string|max:150',
            'cep'         => 'nullable|string|max:12',
            'logradouro'  => 'nullable|string|max:150',
            'numero'      => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:80',
            'bairro'      => 'nullable|string|max:80',
            'cidade'      => 'nullable|string|max:80',
            'uf'          => 'nullable|string|size:2',
            'pais'        => 'nullable|string|max:80',
            'segment_id'  => 'nullable|exists:customer_segments,id',
            'limite_credito' => 'nullable|numeric|min:0',
            'bloqueado'   => 'boolean',
            'tabela_preco'=> 'nullable|string|max:60',
            'status'      => 'required|in:0,1',
            'observacoes' => 'nullable|string',
        ];
    }
}
