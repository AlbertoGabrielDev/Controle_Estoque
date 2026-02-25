<?php

namespace Modules\Suppliers\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Suppliers\Models\Fornecedor;

class FornecedorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        for ($i = 1; $i <= 31; $i++) {
            $nome = $faker->company . ' ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            Fornecedor::query()->firstOrCreate(
                ['nome_fornecedor' => $nome],
                [
                    'logradouro' => $faker->streetName(),
                    'numero_casa' => (string) $faker->numberBetween(1, 9999),
                    'bairro' => $faker->word(),
                    'cidade' => $faker->city(),
                    'uf' => $faker->stateAbbr(),
                    'cep' => $faker->numerify('#####-###'),
                    'cnpj' => $faker->numerify('##.###.###/####-##'),
                    'email' => $faker->unique()->safeEmail(),
                    'status' => 1,
                    'id_users_fk' => 1,
                ]
            );
        }
    }
}
