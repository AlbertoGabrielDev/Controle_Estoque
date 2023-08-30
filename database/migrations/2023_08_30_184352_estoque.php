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
    
        Schema::create('estoque', function(Blueprint $table){
            $table->bigIncrements('id_estoque');
            $table->integer('quantidade');
            $table->string('localizacao');
            $table->dateTime('data_entrega');
            $table->dateTime('data_cadastro');
            $table->unsignedBigInteger('id_cod_produto_fk');
            $table->unsignedBigInteger('id_usuario_fk');
            $table->unsignedBigInteger('id_categoria_fk');
            $table->unsignedBigInteger('id_marca_fk');
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
