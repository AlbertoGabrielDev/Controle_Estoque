<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('fornecedor' , function(Blueprint $table){
            $table->boolean('status')->default(true)->change();
        }); 

        Schema::table('users' , function(Blueprint $table){
            $table->boolean('status')->default(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('fornecedor' , function(Blueprint $table){
            $table->boolean('status')->change();
        }); 

        Schema::table('users' , function(Blueprint $table){
            $table->boolean('status')->change();
        }); 
    }
};
