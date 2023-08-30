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
        Schema::create('fornecedor', function(Blueprint $table){
            $table->bigIncrements('id_fornecedor');
            $table->string('nome_fornecedor');
            $table->integer('preco_fornecedor');
            $table->unsignedBigInteger('id_cod_produto_fk');
            $table->unsignedBigInteger('id_usuario_fk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fornecedor');
    }
};
