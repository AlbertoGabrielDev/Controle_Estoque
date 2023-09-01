<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produto', function(Blueprint $table){
            $table->foreign('id_marca_fk')->references('id_marca')->on('marca');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto', function(Blueprint $table){
            $table->dropColumn('id_marca_fk');

        });
    }
};
