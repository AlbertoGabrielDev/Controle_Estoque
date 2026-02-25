<?php

namespace Modules\Suppliers\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Suppliers\Models\Fornecedor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Suppliers\Models\Fornecedor>
 */
class FornecedorFactory extends Factory
{
    protected $model = Fornecedor::class;

    public function definition(): array
    {
        $fornecedores = [
            ['nome' => 'Distribuidora Central', 'cidade' => 'Sao Paulo', 'uf' => 'SP'],
            ['nome' => 'Alimentos do Brasil', 'cidade' => 'Campinas', 'uf' => 'SP'],
            ['nome' => 'MultiMix Distribuidora', 'cidade' => 'Rio de Janeiro', 'uf' => 'RJ'],
            ['nome' => 'Mercantil Vitoria', 'cidade' => 'Vitoria', 'uf' => 'ES'],
            ['nome' => 'Nordeste Fornecimentos', 'cidade' => 'Recife', 'uf' => 'PE'],
            ['nome' => 'Sul Minas Alimentos', 'cidade' => 'Belo Horizonte', 'uf' => 'MG'],
            ['nome' => 'MegaAtacadista', 'cidade' => 'Curitiba', 'uf' => 'PR'],
            ['nome' => 'PremiumFood', 'cidade' => 'Brasilia', 'uf' => 'DF'],
            ['nome' => 'Delicias Cariocas', 'cidade' => 'Niteroi', 'uf' => 'RJ'],
            ['nome' => 'Primeira Opcao', 'cidade' => 'Goiania', 'uf' => 'GO'],
            ['nome' => 'Paulista Distribuicao', 'cidade' => 'Santos', 'uf' => 'SP'],
            ['nome' => 'Alfa Fornecedores', 'cidade' => 'Belem', 'uf' => 'PA'],
            ['nome' => 'Verde Vale Alimentos', 'cidade' => 'Campo Grande', 'uf' => 'MS'],
            ['nome' => 'Grao Fino Comercio', 'cidade' => 'Salvador', 'uf' => 'BA'],
            ['nome' => 'Doce Vida', 'cidade' => 'Joinville', 'uf' => 'SC'],
            ['nome' => 'Norte Distribuicao', 'cidade' => 'Manaus', 'uf' => 'AM'],
            ['nome' => 'Sudeste Importadora', 'cidade' => 'Uberlandia', 'uf' => 'MG'],
            ['nome' => 'Aromas da Serra', 'cidade' => 'Caxias do Sul', 'uf' => 'RS'],
            ['nome' => 'Tropical Foods', 'cidade' => 'Fortaleza', 'uf' => 'CE'],
            ['nome' => 'Top Max', 'cidade' => 'Porto Alegre', 'uf' => 'RS'],
            ['nome' => 'Superline Fornecimentos', 'cidade' => 'Osasco', 'uf' => 'SP'],
            ['nome' => 'MultiAlfa', 'cidade' => 'Ribeirao Preto', 'uf' => 'SP'],
            ['nome' => 'Fornecedora Elite', 'cidade' => 'Aracaju', 'uf' => 'SE'],
            ['nome' => 'Bonanza Alimentos', 'cidade' => 'Florianopolis', 'uf' => 'SC'],
            ['nome' => 'Distribuidora Pioneira', 'cidade' => 'Natal', 'uf' => 'RN'],
            ['nome' => 'Vale Dourado', 'cidade' => 'Maceio', 'uf' => 'AL'],
            ['nome' => 'Comercial RioDoce', 'cidade' => 'Vitoria', 'uf' => 'ES'],
            ['nome' => 'Max Forte', 'cidade' => 'Sao Luis', 'uf' => 'MA'],
            ['nome' => 'Emporio Brasil', 'cidade' => 'Teresina', 'uf' => 'PI'],
            ['nome' => 'Nova Opcao Fornecedores', 'cidade' => 'Belem', 'uf' => 'PA'],
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
        ];
    }
}
