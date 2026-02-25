<?php

namespace Modules\Suppliers\Services;

use App\Models\Cidade;
use App\Models\Telefone;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Modules\Suppliers\Models\Fornecedor;
use RuntimeException;

class FornecedorService
{
    public function listCitiesByUf(string $estado): array
    {
        $uf = strtoupper(trim($estado));

        if ($uf === '' || !Schema::hasTable((new Cidade())->getTable())) {
            return [];
        }

        return Cidade::query()
            ->whereRaw('UPPER(uf) = ?', [$uf])
            ->orderBy('nome')
            ->get(['id', 'nome', 'uf'])
            ->map(fn (Cidade $cidade) => [
                'id' => $cidade->id,
                'nome' => $cidade->nome,
                'uf' => $cidade->uf,
            ])
            ->values()
            ->all();
    }

    public function create(array $validated, ?int $userId): Fornecedor
    {
        return DB::transaction(function () use ($validated, $userId) {
            $fornecedor = Fornecedor::query()->create([
                'codigo' => $validated['codigo'],
                'razao_social' => $validated['razao_social'] ?? null,
                'nome_fornecedor' => $validated['nome_fornecedor'],
                'cnpj' => $validated['cnpj'],
                'nif_cif' => $validated['nif_cif'] ?? null,
                'cep' => $validated['cep'],
                'logradouro' => $validated['logradouro'],
                'bairro' => $validated['bairro'],
                'numero_casa' => $validated['numero_casa'],
                'email' => $validated['email'] ?? null,
                'id_users_fk' => $userId,
                'cidade' => $validated['cidade'],
                'uf' => strtoupper((string) $validated['uf']),
                'endereco' => $validated['endereco'] ?? null,
                'prazo_entrega_dias' => $validated['prazo_entrega_dias'] ?? 0,
                'condicao_pagamento' => $validated['condicao_pagamento'] ?? null,
                'ativo' => (bool) $validated['ativo'],
            ]);

            Telefone::query()->create([
                'ddd' => $validated['ddd'],
                'telefone' => $validated['telefone'],
                'principal' => (int) ($validated['principal'] ?? 0),
                'whatsapp' => (int) ($validated['whatsapp'] ?? 0),
                'telegram' => (int) ($validated['telegram'] ?? 0),
                'id_fornecedor_fk' => $fornecedor->id_fornecedor,
            ]);

            return $fornecedor;
        });
    }

    public function findOrFail(int $fornecedorId): Fornecedor
    {
        return Fornecedor::query()->findOrFail($fornecedorId);
    }

    public function findPrimaryPhone(int $fornecedorId): ?Telefone
    {
        return Telefone::query()
            ->where('id_fornecedor_fk', $fornecedorId)
            ->orderBy('id_telefone')
            ->first();
    }

    public function update(int $fornecedorId, array $validated): Fornecedor
    {
        return DB::transaction(function () use ($fornecedorId, $validated) {
            $fornecedor = $this->findOrFail($fornecedorId);

            $fornecedor->update([
                'codigo' => $validated['codigo'],
                'razao_social' => $validated['razao_social'] ?? null,
                'nome_fornecedor' => $validated['nome_fornecedor'],
                'cnpj' => $validated['cnpj'],
                'nif_cif' => $validated['nif_cif'] ?? null,
                'cep' => $validated['cep'],
                'logradouro' => $validated['logradouro'],
                'bairro' => $validated['bairro'],
                'numero_casa' => $validated['numero_casa'],
                'email' => $validated['email'] ?? null,
                'cidade' => $validated['cidade'],
                'uf' => strtoupper((string) $validated['uf']),
                'endereco' => $validated['endereco'] ?? null,
                'prazo_entrega_dias' => $validated['prazo_entrega_dias'] ?? 0,
                'condicao_pagamento' => $validated['condicao_pagamento'] ?? null,
                'ativo' => (bool) $validated['ativo'],
            ]);

            Telefone::query()->updateOrCreate(
                ['id_fornecedor_fk' => $fornecedorId],
                [
                    'ddd' => $validated['ddd'],
                    'telefone' => $validated['telefone'],
                    'principal' => (int) ($validated['principal'] ?? 0),
                    'whatsapp' => (int) ($validated['whatsapp'] ?? 0),
                    'telegram' => (int) ($validated['telegram'] ?? 0),
                ]
            );

            return $fornecedor->refresh();
        });
    }

    public function delete(int $fornecedorId): void
    {
        $fornecedor = $this->findOrFail($fornecedorId);

        if (method_exists($fornecedor, 'produtos') && $fornecedor->produtos()->exists()) {
            throw new RuntimeException('Nao e possivel remover: ha produtos vinculados a este fornecedor.');
        }

        $fornecedor->delete();
    }
}
