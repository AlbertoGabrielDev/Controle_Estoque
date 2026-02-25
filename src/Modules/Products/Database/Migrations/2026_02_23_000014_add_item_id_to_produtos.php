<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('unidade_medida_id');
            }
        });

        if (Schema::hasTable('itens') && Schema::hasColumn('produtos', 'item_id')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->foreign('item_id')
                    ->references('id')
                    ->on('itens')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('produtos', 'item_id')) {
            Schema::table('produtos', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
                $table->dropColumn('item_id');
            });
        }
    }
};
