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
        Schema::table('categoria_produto', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_categoria_fk');
            $table->foreign('id_categoria_fk')->references('id_categoria')->on('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categoria_produto', function (Blueprint $table) {
            $table->dropForeign('id_categoria_fk');
            $table->dropColumn('id_categoria_fk')->references('id_categoria')->on('categoria');
        });
    }
};
