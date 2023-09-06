<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('produto', function(Blueprint $table){
            $table->boolean('validade');
        });
    }

    public function down(): void
    {
        Schema::table('produto', function(Blueprint $table){
            $table->dropColunm('validade');
        });
    }
};
