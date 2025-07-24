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
        Schema::table('users', function(Blueprint $table) {
			$table->unsignedSmallInteger('id_unidade_fk')->nullable();
            $table->foreign('id_unidade_fk')->references('id_unidade')->on('unidades');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_unidade_fk']);
            $table->dropColumn('id_unidade_fk');
        });
    }
};
