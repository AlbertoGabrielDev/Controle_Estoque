<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('telefones', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_fornecedor_fk');
            $table->foreign('id_fornecedor_fk')->references('id_fornecedor')->on('fornecedor');
        });
    }
    public function down(): void
    {
        Schema::table('telefones', function (Blueprint $table) {
            $table->dropColumn('id_fornecedor_fk');
           
        });
    }
};
