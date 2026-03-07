<?php

namespace Tests\Unit\Modules;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Suppliers\Models\Fornecedor;
use Modules\Suppliers\Repositories\FornecedorRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class FornecedorRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test makeDatatableQuery for Fornecedor.
     */
    public function test_make_datatable_query(): void
    {
        $user = User::factory()->create();

        Fornecedor::query()->create([
            'codigo' => 'F001',
            'nome_fornecedor' => 'Fornecedor Ativo',
            'cnpj' => '11111111111111',
            'email' => 'ativo@empresa.com',
            'cep' => '00000000',
            'logradouro' => 'Rua 1',
            'numero_casa' => '123',
            'bairro' => 'Centro',
            'cidade' => 'Sao Paulo',
            'uf' => 'SP',
            'id_users_fk' => $user->id,
            'ativo' => 1,
            'status' => 1,
        ]);

        Fornecedor::query()->create([
            'codigo' => 'F002',
            'nome_fornecedor' => 'Fornecedor Inativo',
            'cnpj' => '22222222222222',
            'email' => 'inativo@empresa.com',
            'cep' => '00000000',
            'logradouro' => 'Rua 2',
            'numero_casa' => '456',
            'bairro' => 'Industrial',
            'cidade' => 'Campinas',
            'uf' => 'SP',
            'id_users_fk' => $user->id,
            'ativo' => 0,
            'status' => 0,
        ]);

        /** @var FornecedorRepository $repository */
        $repository = app(FornecedorRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['ativo' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Fornecedor Ativo')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Inativo']);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Fornecedor Inativo')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('c2', $columns);
    }
}
