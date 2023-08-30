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
        Schema::table('marca', function (Blueprint $table) {
         
            $table->foreign('id_cod_produto_fk')->references('cod_produto')->on('produto');
            $table->foreign('id_usuario_fk')->references('id_usuario')->on('usuario');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
