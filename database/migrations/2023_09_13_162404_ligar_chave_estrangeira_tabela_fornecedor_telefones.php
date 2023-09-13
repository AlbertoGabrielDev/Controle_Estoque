<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fornecedor', function(Blueprint $table){
            $table->foreign('id_telefone_fk')->references('id_telefone')->on('telefones');
        });
    }
    public function down(): void
    {
        Schema::table('fornecedor', function (Blueprint $table) {
            $table->dropColumn('id_telefone_fk')->references('id_telefone')->on('telefones');
        });
    }
};
