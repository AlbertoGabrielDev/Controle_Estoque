<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estoque', function (Blueprint $table) {
            $table->smallIncrements('id_estoque');
            $table->integer('preco_custo')->length(14);
            $table->integer('preco_venda')->length(14);
            $table->date('data_validade')->nullable(false);
            $table->integer('quantidade');
            $table->date('data_chegada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoque');
    }
};
