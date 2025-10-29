<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('taxes')) {
            Schema::create('taxes', function (Blueprint $table) {
                $table->id();
                $table->string('codigo')->unique(); // ex.: VAT, ICMS
                $table->string('nome');
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tax_rules')) {
            Schema::create('tax_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tax_id')->constrained('taxes')->cascadeOnDelete();

                // Escopo/condições (nullable = wildcard)
                $table->foreignId('segment_id')->nullable()->constrained('customer_segments')->nullOnDelete();
                $table->unsignedBigInteger('categoria_produto_id')->nullable(); // se você usar categorias
                $table->string('ncm_padrao', 32)->nullable();
                $table->string('uf_origem', 2)->nullable();
                $table->string('uf_destino', 2)->nullable();
                $table->string('canal', 40)->nullable();        // loja, ecommerce, marketplace
                $table->string('tipo_operacao', 40)->nullable(); // venda, devolucao...

                // Vigência
                $table->date('vigencia_inicio')->nullable();
                $table->date('vigencia_fim')->nullable();

                // Cálculo
                $table->decimal('aliquota_percent', 8, 4)->nullable(); // 18.0000
                $table->enum('base_formula', ['valor','valor_menos_desc','valor_mais_frete','personalizada'])
                      ->default('valor_menos_desc');
                $table->text('expression')->nullable(); // para 'personalizada'

                // Controle
                $table->unsignedInteger('prioridade')->default(100); // menor = aplica antes
                $table->boolean('cumulativo')->default(false);       // pode somar várias regras do mesmo imposto

                $table->timestamps();

                // Índices úteis para busca de regra
                $table->index(['segment_id','categoria_produto_id','ncm_padrao']);
                $table->index(['uf_origem','uf_destino','canal','tipo_operacao']);
                $table->index(['vigencia_inicio','vigencia_fim']);
                $table->index(['prioridade']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
        Schema::dropIfExists('taxes');
    }
};
