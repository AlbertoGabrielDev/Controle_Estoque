<?php

namespace Tests\Feature\Modules;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Finance\Models\CentroCusto;
use Modules\Finance\Models\ContaContabil;
use Modules\Finance\Models\Despesa;
use Modules\Suppliers\Models\Fornecedor;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class FinanceModulePhaseFiveFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_flow_creates_expense_with_relations(): void
    {
        $user = User::factory()->create();
        $suffix = Str::random(6);

        $centro = CentroCusto::query()->create([
            'codigo' => 'CC' . $suffix,
            'nome' => 'Centro ' . $suffix,
            'ativo' => true,
        ]);

        $conta = ContaContabil::query()->create([
            'codigo' => 'CT' . $suffix,
            'nome' => 'Conta ' . $suffix,
            'tipo' => 'despesa',
            'aceita_lancamento' => true,
            'ativo' => true,
        ]);

        $fornecedor = Fornecedor::query()->create([
            'codigo' => 'FOR' . $suffix,
            'razao_social' => 'Fornecedor ' . $suffix,
            'nome_fornecedor' => 'Fornecedor ' . $suffix,
            'cnpj' => '12.345.678/0001-' . random_int(10, 99),
            'nif_cif' => 'NIF' . $suffix,
            'cep' => '01001-000',
            'logradouro' => 'Rua A',
            'bairro' => 'Centro',
            'numero_casa' => '123',
            'cidade' => 'Sao Paulo',
            'uf' => 'SP',
            'email' => 'fornecedor_' . $suffix . '@example.com',
            'id_users_fk' => $user->id,
            'ativo' => true,
            'status' => 1,
        ]);

        $despesa = Despesa::query()->create([
            'data' => now()->toDateString(),
            'descricao' => 'Despesa ' . $suffix,
            'valor' => 120.50,
            'centro_custo_id' => $centro->id,
            'conta_contabil_id' => $conta->id,
            'fornecedor_id' => $fornecedor->id_fornecedor,
            'documento' => 'NF-' . $suffix,
            'ativo' => true,
        ]);

        $this->assertDatabaseHas('despesas', [
            'id' => $despesa->id,
            'descricao' => $despesa->descricao,
        ]);
        $this->assertSame($centro->id, $despesa->centroCusto->id);
        $this->assertSame($conta->id, $despesa->contaContabil->id);
        $this->assertSame($fornecedor->id_fornecedor, $despesa->fornecedor->id_fornecedor);
    }
}
