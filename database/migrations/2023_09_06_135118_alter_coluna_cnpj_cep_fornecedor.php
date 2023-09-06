<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('fornecedor', function (Blueprint $table){
            $table->string('cnpj',14)->change();
            $table->string('cep',10)->change();
        });
    }

    public function down(): void
    {
        Schema::table('fornecedor', function (Blueprint $table){
            $table->int('cnpj')->change();
            $table->int('cep')->change();
        });
    }
};
