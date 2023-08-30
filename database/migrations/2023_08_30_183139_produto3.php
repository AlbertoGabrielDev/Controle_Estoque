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
        Schema::create('produto', function(Blueprint $table){
            $table->bigIncrements('cod_produto')->unique();
            $table->string('nome_produto',100);
            $table->string('descricao',500);
            $table->dateTime('validade');
            $table->integer('lote');
            $table->string('unidade_medida',2);
            $table->float('preco_produto',5,2);
            $table->unsignedBigInteger('id_usuario_fk');
            // $table->foreign('id_usuario_fk')->references('usuario')->on('id_usuario');
            $table->timestamps();
        });       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto');
    }
};
