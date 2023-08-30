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
    
        Schema::create('inf_nutri', function(Blueprint $table){
            $table->bigIncrements('id_nuri');
            $table->integer('valor_energetico');
            $table->integer('carboidrato');
            $table->integer('proteina');
            $table->integer('sodio');
            $table->unsignedBigInteger('id_cod_produto_fk');
            //$table->foreign('id_cod_produto_fk')->references('produto')->on('cod_produto');
            $table->unsignedBigInteger('id_usuario_fk');
            //$table->foreign('id_usuario_fk')->references('usuario')->on('id_usuario');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inf_nutri');
    }
};
