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
        Schema::table('historicos', function (Blueprint $table) {

            $table->decimal('venda', 10, 2)->after('quantidade_historico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historico', function (Blueprint $table) {
            $table->dropColumn('venda');
        });
    }
};
