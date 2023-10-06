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
        Schema::create('fornecedor', function (Blueprint $table) {
            $table->smallIncrements('id_fornecedor');
            $table->string('nome_fornecedor',60)->unique();
            $table->string('cnpj',18)->unique();
            $table->string('cep',10);
            $table->string('logradouro',50);
            $table->string('bairro',50);
            $table->string('numero_casa',15);
            $table->string('cidade',50);
            $table->string('uf',2);
            $table->string('email');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->unsignedSmallInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fornecedor');
    }
};
