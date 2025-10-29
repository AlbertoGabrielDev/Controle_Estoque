<?php

namespace Database\Seeders;

use App\Models\Fornecedor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FornecedorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        for ($i = 1; $i <= 31; $i++) {
            // garante nome único acrescentando um sufixo sequencial
            $nome = $faker->company . ' ' . str_pad((string)$i, 2, '0', STR_PAD_LEFT);

            Fornecedor::firstOrCreate(
                ['nome_fornecedor' => $nome], // chave de unicidade
                [
                    'logradouro'       => $faker->streetName(),
                    'numero_casa'      => (string) $faker->numberBetween(1, 9999),
                    'bairro'           => $faker->word(),
                    'cidade'           => $faker->city(),
                    'uf'               => $faker->stateAbbr(),
                    'cep'              => $faker->numerify('#####-###'),
                    'cnpj'             => $faker->numerify('##.###.###/####-##'),
                    'email'            => $faker->unique()->safeEmail(),
                    'status'           => 1,
                    'id_users_fk'      => 1,
                ]
            );
        }
    }
}
