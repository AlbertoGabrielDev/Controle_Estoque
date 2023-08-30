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
        Schema::table('estoque', function (Blueprint $table) {
         
            $table->unsignedBigInteger('id_cod_produto_fk');
            $table->unsignedBigInteger('id_usuario_fk');
            $table->unsignedBigInteger('id_marca_fk');
            $table->unsignedBigInteger('id_categoria_fk');

            $table->foreign('id_cod_produto_fk')->references('cod_produto')->on('produto');
            $table->foreign('id_usuario_fk')->references('id_usuario')->on('usuario');
            $table->foreign('id_marca_fk')->references('id_marca')->on('marca');
            $table->foreign('id_categoria_fk')->references('id_categoria')->on('categoria');
                
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
