<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('estoque', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_marca_fk');
            $table->foreign('id_marca_fk')->references('id_marca')->on('marca');
        });
    }

    public function down(): void
    {
        Schema::table('estoque', function (Blueprint $table) {
            $table->dropForeign('id_marca_fk');
            $table->dropColumn('id_marca_fk')->references('id_marca')->on('marca');
        });
    }
};
