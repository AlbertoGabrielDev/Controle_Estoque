<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos', 'unidade_medida_id')) {
                $table->unsignedBigInteger('unidade_medida_id')->nullable()->after('unidade_medida');
            }
        });

        if (Schema::hasTable('unidades_medida') && Schema::hasColumn('produtos', 'unidade_medida_id')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->foreign('unidade_medida_id')
                    ->references('id')
                    ->on('unidades_medida')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('produtos', 'unidade_medida_id')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->dropForeign(['unidade_medida_id']);
                $table->dropColumn('unidade_medida_id');
            });
        }
    }
};
