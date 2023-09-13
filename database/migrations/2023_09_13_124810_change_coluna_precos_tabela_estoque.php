<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::table('estoque', function(Blueprint $table){
            $table->float('preco_venda')->change();
            $table->float('preco_custo')->change();
        });
    }

    public function down(): void
    {
        Schema::table('estoque', function(Blueprint $table){
            $table->int('preco_venda')->change();
            $table->int('preco_custo')->change();
        });
    }
};
