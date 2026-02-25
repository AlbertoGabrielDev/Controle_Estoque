<?php

namespace Modules\Customers\Database\Seeders;

use App\Models\CustomerSegment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Modules\Customers\Models\Cliente;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Garante segmentos básicos
        $varejo  = CustomerSegment::firstOrCreate(['nome' => 'Varejo']);
        $atacado = CustomerSegment::firstOrCreate(['nome' => 'Atacado']);
        $segmentosIds = [$varejo->id, $atacado->id];

        // Tenta obter um usuário para vincular (id_users_fk)
        $anyUserId = User::query()->value('id'); // null se não existir

        // CEPS reais (capitais e regiões centrais)
        $cepsValidos = [
            '01001-000','01310-100','02011-000', // SP - São Paulo
            '20010-000','20211-110',             // RJ - Rio de Janeiro
            '30110-000','30190-000',             // MG - Belo Horizonte
            '40010-000','40110-000',             // BA - Salvador
            '60010-000','60110-000',             // CE - Fortaleza
            '70040-010','70710-100',             // DF - Brasília
            '80010-000','80210-070',             // PR - Curitiba
            '88010-400','88015-240',             // SC - Florianópolis
            '90010-000','90619-900',             // RS - Porto Alegre
            '64000-000','64019-800',             // PI - Teresina
            '66010-000','66053-180',             // PA - Belém
            '59010-000','59020-400',             // RN - Natal
            '69005-010','69020-030',             // AM - Manaus
            '69090-415','69099-120',             // AM - Manaus (outros)
        ];

        // UFs
        $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

        // Para evitar CPFs duplicados
        $cpfsGerados = [];

        for ($i = 0; $i < 20; $i++) {
            // Gera CPF válido e único
            $cpf = $this->gerarCpfValido(true);
            while (in_array($cpf, $cpfsGerados, true)) {
                $cpf = $this->gerarCpfValido(true);
            }
            $cpfsGerados[] = $cpf;

            $isPF = true; // pedido: CPFs válidos (PF)
            $nomePF = $faker->name();
            $whats = $this->formataWhatsapp($faker);

            // CEP e UF
            $cep = $cepsValidos[array_rand($cepsValidos)];
            $uf  = $ufs[array_rand($ufs)];

            // Endereço fake coerente
            $logradouro = $faker->streetName();
            $numero     = (string) $faker->numberBetween(1, 3999);
            $bairro     = $faker->citySuffix();
            $cidade     = $faker->city();

            Cliente::create([
                'tipo_pessoa'       => $isPF ? 'PF' : 'PJ',
                'documento'         => $cpf,
                'inscricao_estadual'=> null,
                'razao_social'      => null,
                'nome_fantasia'     => null,
                'nome'              => $nomePF,
                'email'             => $faker->unique()->safeEmail(),
                'whatsapp'          => $whats,
                'telefone'          => $this->formataTelefone($faker),
                'site'              => null,

                'cep'               => $cep,
                'logradouro'        => $logradouro,
                'numero'            => $numero,
                'complemento'       => $faker->optional(0.3)->secondaryAddress(),
                'bairro'            => ucfirst($bairro),
                'cidade'            => $cidade,
                'uf'                => $uf,
                'pais'              => 'Brasil',

                'segment_id'        => $segmentosIds[array_rand($segmentosIds)],
                'limite_credito'    => $faker->randomElement([0, 500, 1000, 2000, 5000, 10000]),
                'bloqueado'         => false,
                'tabela_preco'      => $faker->randomElement([null, 'PADRAO', 'ATACADO', 'VIP']),
                'id_users_fk'       => $anyUserId, // pode ficar null se não houver usuários
                'status'            => 1,
                'observacoes'       => $faker->optional(0.4)->sentence(10),
            ]);
        }
    }

    /**
     * Gera CPF válido com dígitos verificadores.
     * @param bool $formatar Se true, retorna como 000.000.000-00
     */
    private function gerarCpfValido(bool $formatar = true): string
    {
        // 9 dígitos base aleatórios
        $n = [];
        for ($i = 0; $i < 9; $i++) {
            $n[$i] = random_int(0, 9);
        }

        // DV1
        $soma = 0;
        for ($i = 0, $peso = 10; $i < 9; $i++, $peso--) {
            $soma += $n[$i] * $peso;
        }
        $dv1 = ($soma * 10) % 11;
        $dv1 = ($dv1 == 10) ? 0 : $dv1;

        // DV2
        $soma = 0;
        for ($i = 0, $peso = 11; $i < 9; $i++, $peso--) {
            $soma += $n[$i] * $peso;
        }
        $soma += $dv1 * 2;
        $dv2 = ($soma * 10) % 11;
        $dv2 = ($dv2 == 10) ? 0 : $dv2;

        $digits = implode('', $n) . $dv1 . $dv2;

        if (!$formatar) {
            return $digits;
        }

        // Formatar 000.000.000-00
        return substr($digits, 0, 3) . '.' .
               substr($digits, 3, 3) . '.' .
               substr($digits, 6, 3) . '-' .
               substr($digits, 9, 2);
    }

    private function formataWhatsapp($faker): string
    {
        // +55 11 9XXXX-XXXX
        $ddd = str_pad((string) $faker->numberBetween(11, 99), 2, '0', STR_PAD_LEFT);
        $parte1 = str_pad((string) $faker->numberBetween(90000, 99999), 5, '0', STR_PAD_LEFT);
        $parte2 = str_pad((string) $faker->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT);
        return "+55 {$ddd} {$parte1}-{$parte2}";
    }

    private function formataTelefone($faker): string
    {
        // +55 11 3XXX-XXXX
        $ddd = str_pad((string) $faker->numberBetween(11, 99), 2, '0', STR_PAD_LEFT);
        $parte1 = str_pad((string) $faker->numberBetween(3000, 4999), 4, '0', STR_PAD_LEFT);
        $parte2 = str_pad((string) $faker->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT);
        return "+55 {$ddd} {$parte1}-{$parte2}";
    }
}
