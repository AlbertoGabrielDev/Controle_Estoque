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
            $table->unsignedBigInteger('item_id');
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
                ->cascadeOnDelete();

            $table->unique(['tabela_preco_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabela_preco_itens');
    }
};
