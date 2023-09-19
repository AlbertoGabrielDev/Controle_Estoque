<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('produto', function (Blueprint $table){
            $table->boolean('status')->default('1');
        });
        Schema::table('estoque', function (Blueprint $table){
            $table->boolean('status')->default('1');
        });
        Schema::table('marca', function (Blueprint $table){
            $table->boolean('status')->default('1');
        });
    }

    public function down(): void
    {
       Schema::table('produto', function(Blueprint $table){
            $table->dropColumn('status');
        });
        Schema::table('estoque', function(Blueprint $table){
        $table->dropColumn('status');
        });
        Schema::table('marca', function(Blueprint $table){
        $table->dropColumn('status');
        });
    }
};
