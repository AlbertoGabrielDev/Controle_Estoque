<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            UnidadeSeeder::class,
            MenuSeeder::class,
            PermissionSeeder::class,
            CategoriaSeeder::class,
            ProdutoSeeder::class,
            MarcaSeeder::class,
            FornecedorSeeder::class,
            TelefoneSeeder::class,
            ClientesSeeder::class,
            TaxSeeder::class,
            SupermercadoBasicoTaxesSeeder::class,
            EstoqueSeeder::class,
            VendaSeeder::class,
        ]);

    }
}
