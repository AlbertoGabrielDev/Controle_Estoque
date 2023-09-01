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
        Schema::create('table_produto', function (Blueprint $table) {
            $table->smallIncrements('id_produto');
            $table->string('cod_produto',60)->unique();
            $table->string('nome_produto',50);
            $table->string('descricao',50);
            $table->string('unidade_medida',2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_produto');
    }
};
