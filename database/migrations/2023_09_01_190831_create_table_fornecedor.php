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
        Schema::create('table_fornecedor', function (Blueprint $table) {
            $table->smallIncrements('id_fornecedor');
            $table->string('nome_fornecedor')->unique();
            $table->integer('cnpj')->length(14)->unique();
            $table->integer('cep');
            $table->string('logradouro',50);
            $table->string('bairro',50);
            $table->string('numero_casa',15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_fornecedor');
    }
};
