<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('cidade', function (Blueprint $table) {
            $table->integer('id_estado_fk')->nullable(true);
            $table->foreign('id_estado_fk')->references('id')->on('estado');
        });

        Schema::table('fornecedor', function (Blueprint $table) {
            $table->integer('id_cidade_fk')->nullable(true);
            $table->foreign('id_cidade_fk')->references('id')->on('cidade');
        });
    }

    public function down(): void
    {
        Schema::table('cidade', function (Blueprint $table) {
            $table->dropForeign('id_estado_fk');
            $table->dropColumn('id_estado_fk')->references('id')->on('estado');
        });

        Schema::table('fornecedor', function (Blueprint $table) {
            $table->dropForeign('id_cidade_fk');
            $table->dropColumn('id_cidade_fk')->references('id')->on('cidade');
        });
    }
};
