<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoriaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('ativo') && $this->has('status')) {
            $this->merge(['ativo' => (int) $this->input('status') === 1]);
        }

        if (!$this->has('nome_categoria') && $this->has('categoria')) {
            $this->merge(['nome_categoria' => $this->input('categoria')]);
        }
    }

    public function rules(): array
    {
        $categoria = $this->route('categoriaId') ?? $this->route('categoria');
        $id = is_object($categoria) ? $categoria->id_categoria : $categoria;

        return [
            'codigo' => ['required', 'string', 'max:30', Rule::unique('categorias', 'codigo')->ignore($id, 'id_categoria')],
            'nome_categoria' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:produto,cliente,fornecedor'],
            'categoria_pai_id' => ['nullable', 'integer', 'exists:categorias,id_categoria'],
            'imagem' => ['nullable', 'image', 'max:4096'],
            'ativo' => ['required', 'boolean'],
            'status' => ['nullable', 'in:0,1'],
        ];
    }
}
