<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('cidade', function (Blueprint $table){
            $table->unsignedSmallInteger('id_estado_fk');
            $table->foreign('id_estado_fk')->references('id')->on('estado');
        });
       
    }

    public function down(): void
    {
        Schema::table('cidade', function (Blueprint $table) {
            $table->dropForeign('id_estado_fk');
            $table->dropColumn('id_estado_fk')->references('id')->on('estado');
        });
        
    }
};