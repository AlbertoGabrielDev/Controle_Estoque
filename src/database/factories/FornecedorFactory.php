<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fornecedor>
 */
class FornecedorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fornecedores = [
            ['nome' => 'Distribuidora Central', 'cidade' => 'São Paulo', 'uf' => 'SP'],
            ['nome' => 'Alimentos do Brasil', 'cidade' => 'Campinas', 'uf' => 'SP'],
            ['nome' => 'MultiMix Distribuidora', 'cidade' => 'Rio de Janeiro', 'uf' => 'RJ'],
            ['nome' => 'Mercantil Vitória', 'cidade' => 'Vitória', 'uf' => 'ES'],
            ['nome' => 'Nordeste Fornecimentos', 'cidade' => 'Recife', 'uf' => 'PE'],
            ['nome' => 'Sul Minas Alimentos', 'cidade' => 'Belo Horizonte', 'uf' => 'MG'],
            ['nome' => 'MegaAtacadista', 'cidade' => 'Curitiba', 'uf' => 'PR'],
            ['nome' => 'PremiumFood', 'cidade' => 'Brasília', 'uf' => 'DF'],
            ['nome' => 'Delícias Cariocas', 'cidade' => 'Niterói', 'uf' => 'RJ'],
            ['nome' => 'Primeira Opção', 'cidade' => 'Goiânia', 'uf' => 'GO'],
            ['nome' => 'Paulista Distribuição', 'cidade' => 'Santos', 'uf' => 'SP'],
            ['nome' => 'Alfa Fornecedores', 'cidade' => 'Belém', 'uf' => 'PA'],
            ['nome' => 'Verde Vale Alimentos', 'cidade' => 'Campo Grande', 'uf' => 'MS'],
            ['nome' => 'Grão Fino Comércio', 'cidade' => 'Salvador', 'uf' => 'BA'],
            ['nome' => 'Doce Vida', 'cidade' => 'Joinville', 'uf' => 'SC'],
            ['nome' => 'Norte Distribuição', 'cidade' => 'Manaus', 'uf' => 'AM'],
            ['nome' => 'Sudeste Importadora', 'cidade' => 'Uberlândia', 'uf' => 'MG'],
            ['nome' => 'Aromas da Serra', 'cidade' => 'Caxias do Sul', 'uf' => 'RS'],
            ['nome' => 'Tropical Foods', 'cidade' => 'Fortaleza', 'uf' => 'CE'],
            ['nome' => 'Top Max', 'cidade' => 'Porto Alegre', 'uf' => 'RS'],
            ['nome' => 'Superline Fornecimentos', 'cidade' => 'Osasco', 'uf' => 'SP'],
            ['nome' => 'MultiAlfa', 'cidade' => 'Ribeirão Preto', 'uf' => 'SP'],
            ['nome' => 'Fornecedora Elite', 'cidade' => 'Aracaju', 'uf' => 'SE'],
            ['nome' => 'Bonanza Alimentos', 'cidade' => 'Florianópolis', 'uf' => 'SC'],
            ['nome' => 'Distribuidora Pioneira', 'cidade' => 'Natal', 'uf' => 'RN'],
            ['nome' => 'Vale Dourado', 'cidade' => 'Maceió', 'uf' => 'AL'],
            ['nome' => 'Comercial RioDoce', 'cidade' => 'Vitória', 'uf' => 'ES'],
            ['nome' => 'Max Forte', 'cidade' => 'São Luís', 'uf' => 'MA'],
            ['nome' => 'Empório Brasil', 'cidade' => 'Teresina', 'uf' => 'PI'],
            ['nome' => 'Nova Opção Fornecedores', 'cidade' => 'Belém', 'uf' => 'PA'],
        ];

        $fornecedor = $this->faker->unique()->randomElement($fornecedores);

        return [
            'nome_fornecedor' => $fornecedor['nome'],
            'logradouro' => $this->faker->streetName(),
            'numero_casa' => $this->faker->buildingNumber(),
            'bairro' => $this->faker->citySuffix(),
            'cidade' => $fornecedor['cidade'],
            'uf' => $fornecedor['uf'],
            'cep' => $this->faker->postcode(),
            'cnpj' => $this->faker->numerify('##.###.###/####-##'),
            'email' => $this->faker->unique()->safeEmail(),
            'status' => $this->faker->boolean(),
            'id_users_fk' => 1,
            // 'created_at' e 'updated_at' o Laravel preenche sozinho
        ];
    }
}
