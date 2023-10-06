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
        Schema::create('produto', function (Blueprint $table) {
            $table->smallIncrements('id_produto');
            $table->string('cod_produto',60)->unique();
            $table->string('nome_produto',50);
            $table->string('descricao',50);
            $table->json('inf_nutrientes');
            $table->string('unidade_medida',2);
            $table->boolean('status')->default(1);
            $table->date('validade');
            $table->timestamps();

            $table->unsignedSmallInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('produto');
    }
};
