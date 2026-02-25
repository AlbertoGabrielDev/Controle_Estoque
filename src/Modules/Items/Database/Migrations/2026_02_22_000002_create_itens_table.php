<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itens', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 60)->unique();
            $table->string('nome', 120);
            $table->enum('tipo', ['produto', 'servico'])->default('produto');
            $table->unsignedSmallInteger('categoria_id')->nullable();
            $table->unsignedBigInteger('unidade_medida_id')->nullable();
            $table->text('descricao')->nullable();
            $table->decimal('custo', 10, 2)->default(0);
            $table->decimal('preco_base', 10, 2)->default(0);
            $table->decimal('peso_kg', 10, 3)->nullable();
            $table->decimal('volume_m3', 10, 6)->nullable();
            $table->boolean('controla_estoque')->default(true);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->foreign('categoria_id')
                ->references('id_categoria')
                ->on('categorias')
                ->nullOnDelete();

            $table->foreign('unidade_medida_id')
                ->references('id')
                ->on('unidades_medida')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens');
    }
};
