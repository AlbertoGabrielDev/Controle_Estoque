<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'cliente_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('cliente_id')->nullable()->after('client');
                $table->foreign('cliente_id')->references('id_cliente')->on('clientes')->nullOnDelete();
                $table->index('cliente_id');
            });
        }

        if (Schema::hasTable('carts') && !Schema::hasColumn('carts', 'cliente_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->unsignedBigInteger('cliente_id')->nullable()->after('client');
                $table->foreign('cliente_id')->references('id_cliente')->on('clientes')->nullOnDelete();
                $table->index('cliente_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'cliente_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['cliente_id']);
                $table->dropColumn('cliente_id');
            });
        }

        if (Schema::hasTable('carts') && Schema::hasColumn('carts', 'cliente_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropForeign(['cliente_id']);
                $table->dropColumn('cliente_id');
            });
        }
    }
};
