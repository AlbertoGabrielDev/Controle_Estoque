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
        Schema::create('estoques', function (Blueprint $table) {
            $table->smallIncrements('id_estoque');
            $table->double('preco_custo',8,2);
            $table->double('preco_venda',8,2);
            $table->integer('quantidade');
            $table->date('data_chegada');
            $table->string('lote',20);
            $table->string('localizacao',10);
            $table->integer('quantidade_aviso');
            $table->date('validade');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->unsignedBigInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');

            $table->unsignedSmallInteger('id_marca_fk')->nullable();
            $table->foreign('id_marca_fk')->references('id_marca')->on('marcas');

            
            $table->unsignedSmallInteger('id_fornecedor_fk')->nullable();
            $table->foreign('id_fornecedor_fk')->references('id_fornecedor')->on('fornecedores');

            
            $table->unsignedSmallInteger('id_produto_fk');
            $table->foreign('id_produto_fk')->references('id_produto')->on('produtos');
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
