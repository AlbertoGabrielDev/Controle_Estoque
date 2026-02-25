<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cart_items')) {
            return;
        }

        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'id_estoque_fk')) {
                $table->unsignedSmallInteger('id_estoque_fk')->nullable()->after('cart_id');
                $table->index('id_estoque_fk', 'cart_items_estoque_idx');
                $table->foreign('id_estoque_fk', 'cart_items_estoque_fk')
                    ->references('id_estoque')
                    ->on('estoques')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('cart_items')) {
            return;
        }

        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'id_estoque_fk')) {
                $table->dropForeign('cart_items_estoque_fk');
                $table->dropIndex('cart_items_estoque_idx');
                $table->dropColumn('id_estoque_fk');
            }
        });
    }
};
