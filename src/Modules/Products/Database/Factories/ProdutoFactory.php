<?php

namespace Modules\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Products\Models\Produto;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Products\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    public static function baseList(): array
    {
        return [
            [
                'nome' => 'Arroz Integral Tipo 1',
                'descricao' => 'Arroz integral de grãos selecionados, fonte de fibras.',
                'inf_nutriente' => [
                    'calorias' => 360,
                    'proteina' => 7.4,
                    'carboidrato' => 77.5,
                    'gordura' => 2.2,
                ]
            ],
            [
                'nome' => 'Feijão Carioca',
                'descricao' => 'Feijão carioca escolhido a dedo, ideal para feijoadas.',
                'inf_nutriente' => [
                    'calorias' => 340,
                    'proteina' => 9.0,
                    'carboidrato' => 61.5,
                    'gordura' => 1.4,
                ]
            ],
            [
                'nome' => 'Macarrão Espaguete',
                'descricao' => 'Massa italiana tradicional, ideal para o dia a dia.',
                'inf_nutriente' => [
                    'calorias' => 371,
                    'proteina' => 12.0,
                    'carboidrato' => 75.0,
                    'gordura' => 1.5,
                ]
            ],
            [
                'nome' => 'Café Torrado e Moído',
                'descricao' => 'Café 100% arábica, aroma intenso e sabor marcante.',
                'inf_nutriente' => [
                    'calorias' => 2,
                    'proteina' => 0.2,
                    'carboidrato' => 0.3,
                    'gordura' => 0.1,
                ]
            ],
            [
                'nome' => 'Leite UHT Integral',
                'descricao' => 'Leite integral longa vida, pronto para o consumo.',
                'inf_nutriente' => [
                    'calorias' => 61,
                    'proteina' => 3.2,
                    'carboidrato' => 4.7,
                    'gordura' => 3.4,
                ]
            ],
            [
                'nome' => 'Óleo de Soja',
                'descricao' => 'Óleo vegetal refinado, ideal para cozinhar e fritar.',
                'inf_nutriente' => [
                    'calorias' => 900,
                    'proteina' => 0.0,
                    'carboidrato' => 0.0,
                    'gordura' => 100.0,
                ]
            ],
            [
                'nome' => 'Açúcar Refinado',
                'descricao' => 'Açúcar cristal branco, ideal para doces e bebidas.',
                'inf_nutriente' => [
                    'calorias' => 387,
                    'proteina' => 0.0,
                    'carboidrato' => 99.9,
                    'gordura' => 0.0,
                ]
            ],
            [
                'nome' => 'Farinha de Trigo',
                'descricao' => 'Farinha especial para pães, bolos e massas.',
                'inf_nutriente' => [
                    'calorias' => 364,
                    'proteina' => 10.0,
                    'carboidrato' => 76.0,
                    'gordura' => 1.0,
                ]
            ],
            [
                'nome' => 'Sal Refinado',
                'descricao' => 'Sal marinho refinado, mais puro e de fácil dissolução.',
                'inf_nutriente' => [
                    'calorias' => 0,
                    'proteina' => 0.0,
                    'carboidrato' => 0.0,
                    'gordura' => 0.0,
                ]
            ],
            [
                'nome' => 'Molho de Tomate Tradicional',
                'descricao' => 'Molho pronto de tomates frescos, ideal para massas.',
                'inf_nutriente' => [
                    'calorias' => 29,
                    'proteina' => 1.3,
                    'carboidrato' => 6.0,
                    'gordura' => 0.3,
                ]
            ],
            [
                'nome' => 'Biscoito de Polvilho',
                'descricao' => 'Biscoito crocante e leve, tradicional das festas.',
                'inf_nutriente' => [
                    'calorias' => 438,
                    'proteina' => 1.0,
                    'carboidrato' => 77.0,
                    'gordura' => 13.0,
                ]
            ],
            [
                'nome' => 'Presunto Cozido',
                'descricao' => 'Presunto de alta qualidade, perfeito para sanduíches.',
                'inf_nutriente' => [
                    'calorias' => 126,
                    'proteina' => 16.0,
                    'carboidrato' => 2.0,
                    'gordura' => 6.0,
                ]
            ],
            [
                'nome' => 'Queijo Mussarela Fatiado',
                'descricao' => 'Queijo de sabor suave, ideal para pizzas e lanches.',
                'inf_nutriente' => [
                    'calorias' => 285,
                    'proteina' => 20.0,
                    'carboidrato' => 3.0,
                    'gordura' => 21.0,
                ]
            ],
            [
                'nome' => 'Iogurte Natural',
                'descricao' => 'Iogurte integral sem açúcar, saudável e saboroso.',
                'inf_nutriente' => [
                    'calorias' => 61,
                    'proteina' => 3.5,
                    'carboidrato' => 4.7,
                    'gordura' => 3.3,
                ]
            ],
            [
                'nome' => 'Pão Francês',
                'descricao' => 'Pão fresco e crocante, feito diariamente.',
                'inf_nutriente' => [
                    'calorias' => 270,
                    'proteina' => 8.0,
                    'carboidrato' => 53.0,
                    'gordura' => 1.4,
                ]
            ],
            [
                'nome' => 'Frango Resfriado',
                'descricao' => 'Corte de frango fresco, perfeito para o dia a dia.',
                'inf_nutriente' => [
                    'calorias' => 215,
                    'proteina' => 18.0,
                    'carboidrato' => 0.0,
                    'gordura' => 15.0,
                ]
            ],
            [
                'nome' => 'Carne Moída de Boi',
                'descricao' => 'Carne bovina moída, ideal para bolonhesa e hambúrguer.',
                'inf_nutriente' => [
                    'calorias' => 250,
                    'proteina' => 17.0,
                    'carboidrato' => 0.0,
                    'gordura' => 20.0,
                ]
            ],
            [
                'nome' => 'Peito de Peru Fatiado',
                'descricao' => 'Peito de peru defumado, leve e saudável.',
                'inf_nutriente' => [
                    'calorias' => 102,
                    'proteina' => 19.0,
                    'carboidrato' => 1.5,
                    'gordura' => 2.0,
                ]
            ],
            [
                'nome' => 'Ovos Brancos',
                'descricao' => 'Ovos frescos de galinha, fonte de proteína.',
                'inf_nutriente' => [
                    'calorias' => 143,
                    'proteina' => 13.0,
                    'carboidrato' => 1.1,
                    'gordura' => 9.5,
                ]
            ],
            [
                'nome' => 'Banana Prata',
                'descricao' => 'Bananas selecionadas, doces e nutritivas.',
                'inf_nutriente' => [
                    'calorias' => 89,
                    'proteina' => 1.1,
                    'carboidrato' => 22.8,
                    'gordura' => 0.3,
                ]
            ],
            [
                'nome' => 'Maçã Fuji',
                'descricao' => 'Maçãs frescas, crocantes e adocicadas.',
                'inf_nutriente' => [
                    'calorias' => 52,
                    'proteina' => 0.3,
                    'carboidrato' => 14.0,
                    'gordura' => 0.2,
                ]
            ],
            [
                'nome' => 'Alface Crespa',
                'descricao' => 'Alface fresca e crocante, ideal para saladas.',
                'inf_nutriente' => [
                    'calorias' => 15,
                    'proteina' => 1.4,
                    'carboidrato' => 2.9,
                    'gordura' => 0.2,
                ]
            ],
            [
                'nome' => 'Tomate Italiano',
                'descricao' => 'Tomate vermelho e suculento, perfeito para molhos.',
                'inf_nutriente' => [
                    'calorias' => 18,
                    'proteina' => 0.9,
                    'carboidrato' => 3.9,
                    'gordura' => 0.2,
                ]
            ],
            [
                'nome' => 'Batata Inglesa',
                'descricao' => 'Batatas frescas, ótimas para fritar ou cozinhar.',
                'inf_nutriente' => [
                    'calorias' => 77,
                    'proteina' => 2.0,
                    'carboidrato' => 17.0,
                    'gordura' => 0.1,
                ]
            ],
            [
                'nome' => 'Cenoura',
                'descricao' => 'Cenouras selecionadas, ricas em vitamina A.',
                'inf_nutriente' => [
                    'calorias' => 41,
                    'proteina' => 0.9,
                    'carboidrato' => 9.6,
                    'gordura' => 0.2,
                ]
            ],
            [
                'nome' => 'Cebola Roxa',
                'descricao' => 'Cebolas roxas, sabor marcante e levemente adocicado.',
                'inf_nutriente' => [
                    'calorias' => 40,
                    'proteina' => 1.1,
                    'carboidrato' => 9.3,
                    'gordura' => 0.1,
                ]
            ],
            [
                'nome' => 'Azeite de Oliva Extra Virgem',
                'descricao' => 'Azeite de oliva prensado a frio, sabor intenso.',
                'inf_nutriente' => [
                    'calorias' => 884,
                    'proteina' => 0.0,
                    'carboidrato' => 0.0,
                    'gordura' => 100.0,
                ]
            ],
            [
                'nome' => 'Manteiga com Sal',
                'descricao' => 'Manteiga de primeira qualidade, cremosa e saborosa.',
                'inf_nutriente' => [
                    'calorias' => 717,
                    'proteina' => 0.9,
                    'carboidrato' => 0.1,
                    'gordura' => 81.0,
                ]
            ],
            [
                'nome' => 'Iogurte Grego',
                'descricao' => 'Iogurte cremoso, alto teor de proteína.',
                'inf_nutriente' => [
                    'calorias' => 59,
                    'proteina' => 10.0,
                    'carboidrato' => 3.6,
                    'gordura' => 0.4,
                ]
            ],
            [
                'nome' => 'Refrigerante Cola',
                'descricao' => 'Bebida gaseificada, sabor cola clássico.',
                'inf_nutriente' => [
                    'calorias' => 42,
                    'proteina' => 0.0,
                    'carboidrato' => 10.6,
                    'gordura' => 0.0,
                ]
            ],
            [
                'nome' => 'Água Mineral sem Gás',
                'descricao' => 'Água mineral pura e refrescante.',
                'inf_nutriente' => [
                    'calorias' => 0,
                    'proteina' => 0.0,
                    'carboidrato' => 0.0,
                    'gordura' => 0.0,
                ]
            ],
            [
                'nome' => 'Cerveja Pilsen',
                'descricao' => 'Cerveja leve e refrescante, puro malte.',
                'inf_nutriente' => [
                    'calorias' => 43,
                    'proteina' => 0.5,
                    'carboidrato' => 3.6,
                    'gordura' => 0.0,
                ]
            ],
            [
                'nome' => 'Suco de Laranja Integral',
                'descricao' => 'Suco natural, sem adição de açúcar.',
                'inf_nutriente' => [
                    'calorias' => 45,
                    'proteina' => 0.7,
                    'carboidrato' => 10.4,
                    'gordura' => 0.2,
                ]
            ],
            [
                'nome' => 'Achocolatado em Pó',
                'descricao' => 'Pó para preparo de bebidas achocolatadas.',
                'inf_nutriente' => [
                    'calorias' => 380,
                    'proteina' => 5.0,
                    'carboidrato' => 86.0,
                    'gordura' => 1.0,
                ]
            ],
            [
                'nome' => 'Biscoito Recheado Chocolate',
                'descricao' => 'Biscoito crocante com recheio sabor chocolate.',
                'inf_nutriente' => [
                    'calorias' => 489,
                    'proteina' => 4.8,
                    'carboidrato' => 72.0,
                    'gordura' => 19.0,
                ]
            ],
            [
                'nome' => 'Chocolate ao Leite',
                'descricao' => 'Chocolate ao leite cremoso e macio.',
                'inf_nutriente' => [
                    'calorias' => 535,
                    'proteina' => 7.5,
                    'carboidrato' => 59.0,
                    'gordura' => 30.0,
                ]
            ],
            [
                'nome' => 'Pipoca de Micro-ondas',
                'descricao' => 'Pipoca para preparo rápido, sabor amanteigado.',
                'inf_nutriente' => [
                    'calorias' => 550,
                    'proteina' => 7.8,
                    'carboidrato' => 55.0,
                    'gordura' => 34.0,
                ]
            ],
            [
                'nome' => 'Batata Palha',
                'descricao' => 'Batata cortada em tiras finas e crocantes.',
                'inf_nutriente' => [
                    'calorias' => 532,
                    'proteina' => 5.7,
                    'carboidrato' => 60.0,
                    'gordura' => 30.0,
                ]
            ],
            [
                'nome' => 'Hambúrguer Bovino Congelado',
                'descricao' => 'Hambúrguer pronto para preparo, feito com carne bovina.',
                'inf_nutriente' => [
                    'calorias' => 250,
                    'proteina' => 14.0,
                    'carboidrato' => 3.0,
                    'gordura' => 19.0,
                ]
            ],
            [
                'nome' => 'Nuggets de Frango',
                'descricao' => 'Empanados de frango crocantes e práticos.',
                'inf_nutriente' => [
                    'calorias' => 270,
                    'proteina' => 12.0,
                    'carboidrato' => 17.0,
                    'gordura' => 16.0,
                ]
            ],
            [
                'nome' => 'Pizza de Calabresa Congelada',
                'descricao' => 'Pizza congelada pronta para assar, sabor calabresa.',
                'inf_nutriente' => [
                    'calorias' => 250,
                    'proteina' => 11.0,
                    'carboidrato' => 28.0,
                    'gordura' => 11.0,
                ]
            ],
            [
                'nome' => 'Salsicha Viena',
                'descricao' => 'Salsicha tipo viena, ótima para hot dog.',
                'inf_nutriente' => [
                    'calorias' => 300,
                    'proteina' => 12.0,
                    'carboidrato' => 2.0,
                    'gordura' => 27.0,
                ]
            ],
            [
                'nome' => 'Sardinha em Óleo',
                'descricao' => 'Sardinha em conserva, ótima fonte de ômega 3.',
                'inf_nutriente' => [
                    'calorias' => 250,
                    'proteina' => 25.0,
                    'carboidrato' => 0.0,
                    'gordura' => 17.0,
                ]
            ],
            [
                'nome' => 'Atum Sólido em Água',
                'descricao' => 'Atum sólido conservado em água, leve e saudável.',
                'inf_nutriente' => [
                    'calorias' => 116,
                    'proteina' => 26.0,
                    'carboidrato' => 0.0,
                    'gordura' => 1.0,
                ]
            ],
            [
                'nome' => 'Filé de Merluza Congelado',
                'descricao' => 'Filé de merluza congelado, pronto para fritar.',
                'inf_nutriente' => [
                    'calorias' => 94,
                    'proteina' => 20.0,
                    'carboidrato' => 0.0,
                    'gordura' => 1.5,
                ]
            ],
            [
                'nome' => 'Camarão Descascado Congelado',
                'descricao' => 'Camarão limpo e descascado, pronto para receitas.',
                'inf_nutriente' => [
                    'calorias' => 99,
                    'proteina' => 24.0,
                    'carboidrato' => 0.2,
                    'gordura' => 0.3,
                ]
            ],
            [
                'nome' => 'Peixe Tilápia Fresca',
                'descricao' => 'Filé de tilápia fresco, sabor suave.',
                'inf_nutriente' => [
                    'calorias' => 96,
                    'proteina' => 20.0,
                    'carboidrato' => 0.0,
                    'gordura' => 1.7,
                ]
            ],
            [
                'nome' => 'Linguiça Calabresa Defumada',
                'descricao' => 'Linguiça calabresa sabor defumado, ideal para feijoadas.',
                'inf_nutriente' => [
                    'calorias' => 384,
                    'proteina' => 18.0,
                    'carboidrato' => 3.0,
                    'gordura' => 34.0,
                ]
            ],
            [
                'nome' => 'Mortadela Tradicional',
                'descricao' => 'Mortadela fatiada, sabor clássico.',
                'inf_nutriente' => [
                    'calorias' => 311,
                    'proteina' => 13.0,
                    'carboidrato' => 4.0,
                    'gordura' => 27.0,
                ]
            ],
            [
                'nome' => 'Filé de Peito de Frango',
                'descricao' => 'Peito de frango limpo, baixo teor de gordura.',
                'inf_nutriente' => [
                    'calorias' => 165,
                    'proteina' => 31.0,
                    'carboidrato' => 0.0,
                    'gordura' => 3.6,
                ]
            ],
            [
                'nome' => 'Linguiça Toscana',
                'descricao' => 'Linguiça suína toscana, ótima para churrasco.',
                'inf_nutriente' => [
                    'calorias' => 320,
                    'proteina' => 15.0,
                    'carboidrato' => 2.0,
                    'gordura' => 28.0,
                ]
            ],
            [
                'nome' => 'Costela Bovina',
                'descricao' => 'Costela bovina fresca, ideal para assados.',
                'inf_nutriente' => [
                    'calorias' => 343,
                    'proteina' => 17.0,
                    'carboidrato' => 0.0,
                    'gordura' => 30.0,
                ]
            ],
            [
                'nome' => 'Picanha Bovina',
                'descricao' => 'Picanha de qualidade premium para churrasco.',
                'inf_nutriente' => [
                    'calorias' => 346,
                    'proteina' => 21.0,
                    'carboidrato' => 0.0,
                    'gordura' => 29.0,
                ]
            ],
            [
                'nome' => 'Coxa de Frango',
                'descricao' => 'Coxas de frango frescas, suculentas.',
                'inf_nutriente' => [
                    'calorias' => 214,
                    'proteina' => 18.0,
                    'carboidrato' => 0.0,
                    'gordura' => 15.0,
                ]
            ],
            [
                'nome' => 'Linguiça de Frango',
                'descricao' => 'Linguiça feita com carne de frango selecionada.',
                'inf_nutriente' => [
                    'calorias' => 280,
                    'proteina' => 13.0,
                    'carboidrato' => 4.0,
                    'gordura' => 24.0,
                ]
            ],
            [
                'nome' => 'Filé Mignon Bovino',
                'descricao' => 'Filé mignon fresco, corte nobre e macio.',
                'inf_nutriente' => [
                    'calorias' => 191,
                    'proteina' => 29.0,
                    'carboidrato' => 0.0,
                    'gordura' => 7.7,
                ]
            ],
            [
                'nome' => 'Peito de Frango Empanado',
                'descricao' => 'Peito de frango empanado, crocante por fora e macio por dentro.',
                'inf_nutriente' => [
                    'calorias' => 240,
                    'proteina' => 17.0,
                    'carboidrato' => 12.0,
                    'gordura' => 13.0,
                ]
            ],
            [
                'nome' => 'Mortadela de Frango',
                'descricao' => 'Mortadela elaborada com carne de frango.',
                'inf_nutriente' => [
                    'calorias' => 220,
                    'proteina' => 10.0,
                    'carboidrato' => 6.0,
                    'gordura' => 17.0,
                ]
            ],
            [
                'nome' => 'Lombo Suíno Temperado',
                'descricao' => 'Lombo suíno já temperado, pronto para assar.',
                'inf_nutriente' => [
                    'calorias' => 230,
                    'proteina' => 21.0,
                    'carboidrato' => 1.0,
                    'gordura' => 15.0,
                ]
            ],
            [
                'nome' => 'Alcatra Bovino',
                'descricao' => 'Alcatra fresca e macia, ideal para grelhar.',
                'inf_nutriente' => [
                    'calorias' => 238,
                    'proteina' => 23.0,
                    'carboidrato' => 0.0,
                    'gordura' => 16.0,
                ]
            ],
            [
                'nome' => 'Carne de Sol',
                'descricao' => 'Carne bovina salgada, típica do nordeste.',
                'inf_nutriente' => [
                    'calorias' => 224,
                    'proteina' => 29.0,
                    'carboidrato' => 0.0,
                    'gordura' => 11.0,
                ]
            ],
            [
                'nome' => 'Bacon em Cubos',
                'descricao' => 'Bacon defumado cortado em cubos, prático para receitas.',
                'inf_nutriente' => [
                    'calorias' => 541,
                    'proteina' => 37.0,
                    'carboidrato' => 1.0,
                    'gordura' => 42.0,
                ]
            ],
            [
                'nome' => 'Toucinho Suíno',
                'descricao' => 'Toucinho tradicional para feijoada e farofa.',
                'inf_nutriente' => [
                    'calorias' => 661,
                    'proteina' => 8.0,
                    'carboidrato' => 0.0,
                    'gordura' => 71.0,
                ]
            ],
            [
                'nome' => 'Frango à Passarinho Congelado',
                'descricao' => 'Frango à passarinho, já temperado e congelado.',
                'inf_nutriente' => [
                    'calorias' => 244,
                    'proteina' => 20.0,
                    'carboidrato' => 1.0,
                    'gordura' => 17.0,
                ]
            ],
            [
                'nome' => 'Linguiça Mista',
                'descricao' => 'Linguiça com carne bovina e suína.',
                'inf_nutriente' => [
                    'calorias' => 309,
                    'proteina' => 15.0,
                    'carboidrato' => 1.0,
                    'gordura' => 28.0,
                ]
            ],
            [
                'nome' => 'Filé de Frango Grelhado Congelado',
                'descricao' => 'Filé de frango já grelhado, prático para refeições rápidas.',
                'inf_nutriente' => [
                    'calorias' => 125,
                    'proteina' => 23.0,
                    'carboidrato' => 0.0,
                    'gordura' => 3.0,
                ]
            ],
            [
                'nome' => 'Hambúrguer de Frango',
                'descricao' => 'Hambúrguer saboroso feito com carne de frango.',
                'inf_nutriente' => [
                    'calorias' => 180,
                    'proteina' => 12.0,
                    'carboidrato' => 5.0,
                    'gordura' => 12.0,
                ]
            ],
            [
                'nome' => 'Queijo Prato Fatiado',
                'descricao' => 'Queijo prato macio, ideal para sanduíches.',
                'inf_nutriente' => [
                    'calorias' => 347,
                    'proteina' => 25.0,
                    'carboidrato' => 1.0,
                    'gordura' => 27.0,
                ]
            ],
            [
                'nome' => 'Queijo Coalho',
                'descricao' => 'Queijo coalho tradicional, ótimo para churrasco.',
                'inf_nutriente' => [
                    'calorias' => 321,
                    'proteina' => 21.0,
                    'carboidrato' => 3.0,
                    'gordura' => 25.0,
                ]
            ],
            [
                'nome' => 'Requeijão Cremoso',
                'descricao' => 'Requeijão cremoso, perfeito para pães e torradas.',
                'inf_nutriente' => [
                    'calorias' => 253,
                    'proteina' => 5.6,
                    'carboidrato' => 5.2,
                    'gordura' => 22.0,
                ]
            ],
            [
                'nome' => 'Margarina com Sal',
                'descricao' => 'Margarina cremosa com sal, fácil de espalhar.',
                'inf_nutriente' => [
                    'calorias' => 717,
                    'proteina' => 0.5,
                    'carboidrato' => 0.8,
                    'gordura' => 81.0,
                ]
            ],
            [
                'nome' => 'Leite Desnatado',
                'descricao' => 'Leite com baixo teor de gordura, saudável.',
                'inf_nutriente' => [
                    'calorias' => 36,
                    'proteina' => 3.0,
                    'carboidrato' => 5.1,
                    'gordura' => 0.2,
                ]
            ],
            [
                'nome' => 'Leite em Pó Integral',
                'descricao' => 'Leite em pó prático, rico em cálcio.',
                'inf_nutriente' => [
                    'calorias' => 496,
                    'proteina' => 26.0,
                    'carboidrato' => 38.0,
                    'gordura' => 27.0,
                ]
            ],
            [
                'nome' => 'Iogurte Sabor Morango',
                'descricao' => 'Iogurte sabor morango, cremoso e delicioso.',
                'inf_nutriente' => [
                    'calorias' => 71,
                    'proteina' => 2.8,
                    'carboidrato' => 12.0,
                    'gordura' => 1.0,
                ]
            ],
            [
                'nome' => 'Doce de Leite Pastoso',
                'descricao' => 'Doce de leite cremoso, ideal para sobremesas.',
                'inf_nutriente' => [
                    'calorias' => 319,
                    'proteina' => 6.0,
                    'carboidrato' => 60.0,
                    'gordura' => 7.0,
                ]
            ],
            [
                'nome' => 'Biscoito Cream Cracker',
                'descricao' => 'Biscoito salgado e leve, crocante na medida certa.',
                'inf_nutriente' => [
                    'calorias' => 437,
                    'proteina' => 8.0,
                    'carboidrato' => 78.0,
                    'gordura' => 10.0,
                ]
            ],
            [
                'nome' => 'Pão de Forma Integral',
                'descricao' => 'Pão de forma integral, rico em fibras.',
                'inf_nutriente' => [
                    'calorias' => 247,
                    'proteina' => 8.5,
                    'carboidrato' => 43.0,
                    'gordura' => 3.5,
                ]
            ],
            [
                'nome' => 'Pão de Queijo Congelado',
                'descricao' => 'Pão de queijo mineiro pronto para assar.',
                'inf_nutriente' => [
                    'calorias' => 300,
                    'proteina' => 6.0,
                    'carboidrato' => 38.0,
                    'gordura' => 13.0,
                ]
            ],
            [
                'nome' => 'Coxinha de Frango Congelada',
                'descricao' => 'Coxinha crocante recheada com frango.',
                'inf_nutriente' => [
                    'calorias' => 289,
                    'proteina' => 10.0,
                    'carboidrato' => 35.0,
                    'gordura' => 13.0,
                ]
            ],
            [
                'nome' => 'Pastel de Carne Congelado',
                'descricao' => 'Pastel recheado com carne temperada.',
                'inf_nutriente' => [
                    'calorias' => 322,
                    'proteina' => 9.0,
                    'carboidrato' => 34.0,
                    'gordura' => 17.0,
                ]
            ],
            [
                'nome' => 'Lasagna Congelada',
                'descricao' => 'Lasanha de carne pronta para aquecer.',
                'inf_nutriente' => [
                    'calorias' => 150,
                    'proteina' => 7.0,
                    'carboidrato' => 15.0,
                    'gordura' => 7.0,
                ]
            ],
            [
                'nome' => 'Polenta Congelada',
                'descricao' => 'Polenta pronta, prática e saborosa.',
                'inf_nutriente' => [
                    'calorias' => 122,
                    'proteina' => 2.1,
                    'carboidrato' => 24.0,
                    'gordura' => 1.4,
                ]
            ],
            [
                'nome' => 'Feijão Preto',
                'descricao' => 'Feijão preto selecionado, ideal para feijoada.',
                'inf_nutriente' => [
                    'calorias' => 341,
                    'proteina' => 8.9,
                    'carboidrato' => 62.4,
                    'gordura' => 0.8,
                ]
            ],
            [
                'nome' => 'Grão-de-bico',
                'descricao' => 'Grão-de-bico pronto para saladas e cozidos.',
                'inf_nutriente' => [
                    'calorias' => 378,
                    'proteina' => 19.0,
                    'carboidrato' => 63.0,
                    'gordura' => 6.0,
                ]
            ],
            [
                'nome' => 'Lentilha',
                'descricao' => 'Lentilha rica em proteína e ferro.',
                'inf_nutriente' => [
                    'calorias' => 352,
                    'proteina' => 25.0,
                    'carboidrato' => 60.0,
                    'gordura' => 1.0,
                ]
            ],
        ];
    }

    public static function normalizeNutritionList(array $data): array
    {
        if (array_is_list($data)) {
            $items = [];
            foreach ($data as $item) {
                if (is_array($item)) {
                    $label = $item['label'] ?? $item['nome'] ?? $item['chave'] ?? $item['key'] ?? $item['nutriente'] ?? null;
                    $value = $item['valor'] ?? $item['value'] ?? $item['quantidade'] ?? $item['qtd'] ?? null;
                    $unit = $item['unidade'] ?? $item['unit'] ?? null;

                    if ($label === null && $value === null) {
                        continue;
                    }

                    $items[] = [
                        'label' => is_string($label) && $label !== '' ? $label : 'Item',
                        'valor' => $value,
                        'unidade' => $unit,
                    ];
                    continue;
                }

                if ($item === null || $item === '') {
                    continue;
                }

                $items[] = [
                    'label' => 'Item',
                    'valor' => $item,
                    'unidade' => null,
                ];
            }

            return $items;
        }

        $normalized = [];
        foreach ($data as $key => $value) {
            $normalized[] = [
                'label' => self::labelFromKey((string) $key),
                'valor' => $value,
                'unidade' => self::unitFromKey((string) $key),
            ];
        }

        return $normalized;
    }

    private static function labelFromKey(string $key): string
    {
        $clean = trim(str_replace('_', ' ', $key));
        return $clean === '' ? 'Item' : ucwords($clean);
    }

    private static function unitFromKey(string $key): ?string
    {
        $map = [
            'calorias' => 'kcal',
            'proteina' => 'g',
            'carboidrato' => 'g',
            'gordura' => 'g',
            'fibra' => 'g',
            'sodio' => 'mg',
            'acucar' => 'g',
        ];

        $k = strtolower(trim($key));
        return $map[$k] ?? null;
    }
    public function definition(): array
    {
        static $indice = 0;

        $produtos = self::baseList();
        $produto = $produtos[$indice % count($produtos)];
        $indice++;

        return [
            'cod_produto' => $this->faker->unique()->bothify('PROD-####'),
            'nome_produto' => $produto['nome'],
            'descricao' => mb_substr($produto['descricao'], 0, 50),
            'inf_nutriente' => self::normalizeNutritionList($produto['inf_nutriente']),
            'unidade_medida' => $this->faker->randomElement(['kg', 'g', 'ml', 'L']),
            'status' => $this->faker->boolean(),
            'id_users_fk' => 1,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Produto $produto) {
            $categorias = \App\Models\Categoria::inRandomOrder()->take(rand(1, 3))->pluck('id_categoria');
            $produto->categorias()->attach($categorias);
        });
    }
}
