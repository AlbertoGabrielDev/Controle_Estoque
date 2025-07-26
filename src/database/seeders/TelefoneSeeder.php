<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TelefoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fornecedores = \App\Models\Fornecedor::all();
        foreach ($fornecedores as $fornecedor) {
            \App\Models\Telefone::factory()->create([
                'id_fornecedor_fk' => $fornecedor->id_fornecedor
            ]);
        }
    }
}
