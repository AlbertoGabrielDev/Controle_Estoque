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
        Schema::create('categoria_produto', function (Blueprint $table) {
            $table->smallIncrements('id_categoria_produto');
            $table->timestamps();

            $table->unsignedSmallInteger('id_produto_fk');
            $table->foreign('id_produto_fk')->references('id_produto')->on('produto');

            $table->unsignedSmallInteger('id_categoria_fk');
            $table->foreign('id_categoria_fk')->references('id_categoria')->on('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_produto');
    }
};
