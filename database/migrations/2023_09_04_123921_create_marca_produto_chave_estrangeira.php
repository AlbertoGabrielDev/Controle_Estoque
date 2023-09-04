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
        Schema::table('marca_produto', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_produto_fk');
            $table->foreign('id_produto_fk')->references('id_produto')->on('produto');

            $table->unsignedSmallInteger('id_marca_fk');
            $table->foreign('id_marca_fk')->references('id_marca')->on('marca');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marca_produto', function (Blueprint $table) {
            $table->dropForeign('id_marca_produto_fk');
            $table->dropColumn('id_marca_produto_fk')->references('id_produto')->on('produto');

            $table->dropForeign('id_marca_fk');
            $table->dropColumn('id_marca_fk')->references('id_marca')->on('marca');
        });
    }
};
