<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('clientes') && Schema::hasTable('customer_segments')) {
            Schema::table('clientes', function (Blueprint $table) {
                if (!Schema::hasColumn('clientes', 'segment_id')) {
                    $table->unsignedBigInteger('segment_id')->nullable()->after('pais');
                }
                $table->index('segment_id');
            });
        }
    }


    public function down(): void
    {
        if (Schema::hasTable('clientes')) {
            Schema::table('clientes', function (Blueprint $table) {
                try {
                    $table->dropForeign('clientes_segment_id_foreign');
                } catch (\Throwable $e) {
                }
                try {
                    $table->dropIndex(['segment_id']);
                } catch (\Throwable $e) {
                }
            });
        }
    }
};
