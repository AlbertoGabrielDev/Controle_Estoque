<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cidade',function(Blueprint $table){
            $table->dropColumn('uf');
        });
    }

    public function down(): void
    {
        Schema::table('cidade',function(Blueprint $table){
            $table->string('uf');
        });

        $update = 'update cidade set uf = id_estado_fk';
        DB::statement($update);
    }
};
