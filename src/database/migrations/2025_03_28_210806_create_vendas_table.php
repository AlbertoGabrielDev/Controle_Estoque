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
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
          
            $table->unsignedSmallInteger('id_produto_fk');
            $table->foreign('id_produto_fk')->references('id_produto')->on('produto');
            $table->foreignId('id_usuario_fk')->constrained('users');
            $table->string('cod_produto',60)->nullable();
            $table->string('unidade_medida',2)->nullable();
            $table->string('nome_produto',50);
            $table->integer('quantidade');
            $table->double('preco_venda',8,2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
