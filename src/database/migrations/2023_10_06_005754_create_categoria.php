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
        Schema::create('categorias', function (Blueprint $table) {
            $table->smallIncrements('id_categoria');
            $table->string('nome_categoria',50);
            $table->string('imagem');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->unsignedBigInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria');
    }
};
