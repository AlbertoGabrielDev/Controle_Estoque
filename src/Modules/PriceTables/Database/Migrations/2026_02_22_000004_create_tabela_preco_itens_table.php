<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabela_preco_itens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tabela_preco_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedSmallInteger('produto_id')->nullable();
            $table->unsignedSmallInteger('marca_id')->nullable();
            $table->unsignedSmallInteger('fornecedor_id')->nullable();
            $table->decimal('preco', 10, 2);
            $table->decimal('desconto_percent', 5, 2)->default(0);
            $table->integer('quantidade_minima')->default(1);
            $table->timestamps();

            $table->foreign('tabela_preco_id')
                ->references('id')
                ->on('tabelas_preco')
                ->cascadeOnDelete();

            $table->foreign('item_id')
                ->references('id')
                ->on('itens')
                ->nullOnDelete();

            $table->foreign('produto_id')
                ->references('id_produto')
                ->on('produtos')
                ->nullOnDelete();

            $table->foreign('marca_id')
                ->references('id_marca')
                ->on('marcas')
                ->nullOnDelete();

            $table->foreign('fornecedor_id')
                ->references('id_fornecedor')
                ->on('fornecedores')
                ->nullOnDelete();

            $table->unique(['tabela_preco_id', 'item_id'], 'tpi_tp_item_uniq');
            $table->unique(['tabela_preco_id', 'produto_id', 'marca_id', 'fornecedor_id'], 'tpi_tp_prod_mf_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabela_preco_itens');
    }
};
