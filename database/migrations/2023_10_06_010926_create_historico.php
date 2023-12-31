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
        Schema::create('historico', function (Blueprint $table) {
            $table->smallIncrements('historico_id');
            $table->integer('quantidade_diminuida');
            $table->integer('quantidade_historico');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->unsignedSmallInteger('id_estoque_fk');
            $table->foreign('id_estoque_fk')->references('id_estoque')->on('estoque');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico');
    }
};