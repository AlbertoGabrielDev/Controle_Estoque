<?php

namespace Modules\Finance\Database\Seeders;

use App\Models\Fornecedor;
use Modules\Finance\Models\CentroCusto;
use Modules\Finance\Models\ContaContabil;
use Modules\Finance\Models\Despesa;
use Illuminate\Database\Seeder;

class DespesaSeeder extends Seeder
{
    public function run(): void
    {
        $centros = CentroCusto::query()
            ->select('id')
            ->where('ativo', 1)
            ->orderBy('id')
            ->get();

        $contas = ContaContabil::query()
            ->select('id')
            ->where('ativo', 1)
            ->where('aceita_lancamento', 1)
            ->where('tipo', 'despesa')
            ->orderBy('id')
            ->get();

        $fornecedores = Fornecedor::query()
            ->select('id_fornecedor')
            ->where('ativo', 1)
            ->orderBy('id_fornecedor')
            ->get();

        if ($centros->isEmpty() || $contas->isEmpty()) {
            $this->command?->warn(
                'DespesaSeeder: centros de custo ou contas contabeis (tipo despesa) nao encontrados.'
            );
            return;
        }

        $descricoes = [
            'Aluguel',
            'Energia eletrica',
            'Agua',
            'Internet',
            'Telefonia',
            'Frete',
            'Combustivel',
            'Manutencao',
            'Material de escritorio',
            'Marketing',
            'Servicos de TI',
            'Consultoria',
            'Seguranca',
            'Limpeza',
            'Contabilidade',
            'Taxas bancarias',
            'Licencas de software',
            'Treinamentos',
            'Viagens',
            'Impostos',
            'Seguro',
            'Armazenagem',
            'Embalagens',
            'Correios',
        ];

        $detalhes = [
            'mensal',
            'contrato',
            'matriz',
            'filial 1',
            'operacao',
            'campanha',
            'equipamentos',
            'renovacao',
            'eventos',
            'suporte',
            'infraestrutura',
            'projetos',
            'regular',
            'extra',
        ];

        $observacoes = [
            'Pago via boleto',
            'Pago via transferencia',
            'Ajuste de periodo',
            'Servico recorrente',
            'Despesa pontual',
        ];

        $total = 60;
        $baseDate = now()->subDays($total);

        for ($i = 1; $i <= $total; $i++) {
            $centro = $centros[$i % $centros->count()];
            $conta = $contas[$i % $contas->count()];
            $fornecedor = $fornecedores->isNotEmpty()
                ? $fornecedores[$i % $fornecedores->count()]
                : null;

            $doc = sprintf('DESP-%04d', $i);
            $descricao = $descricoes[($i - 1) % count($descricoes)]
                . ' - ' . $detalhes[($i - 1) % count($detalhes)];
            $data = $baseDate->copy()->addDays($i)->toDateString();
            $valor = random_int(1000, 500000) / 100;
            $ativo = $i % 15 !== 0;

            $despesa = Despesa::updateOrCreate(
                ['documento' => $doc],
                [
                    'data' => $data,
                    'descricao' => $descricao,
                    'valor' => $valor,
                    'centro_custo_id' => $centro->id,
                    'conta_contabil_id' => $conta->id,
                    'fornecedor_id' => ($fornecedor && $i % 4 !== 0) ? $fornecedor->id_fornecedor : null,
                    'documento' => $doc,
                    'observacoes' => $i % 3 === 0
                        ? $observacoes[($i - 1) % count($observacoes)]
                        : null,
                    'ativo' => $ativo,
                ]
            );

            $this->command?->info(
                $despesa->wasRecentlyCreated
                    ? "Criada: {$doc}"
                    : "Atualizada: {$doc}"
            );
        }
    }
}
