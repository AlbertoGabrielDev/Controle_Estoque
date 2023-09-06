<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('produto',function(Blueprint $table){
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('produto',function(Blueprint $table){
            $table->dropColumn('updated_at');
            $table->dropColumn('created_at');
        });
    }
};
