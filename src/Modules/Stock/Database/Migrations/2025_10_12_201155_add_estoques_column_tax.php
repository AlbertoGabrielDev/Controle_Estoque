<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('estoques', function (Blueprint $table) {
            $table->decimal('imposto_total', 12, 2)->nullable()->after('preco_venda');
            $table->json('impostos_json')->nullable()->after('imposto_total');
        });
    }


    public function down(): void
    {

        Schema::table('estoques', function (Blueprint $table) {
            $table->dropColumn('imposto_total');
            $table->dropColumn('impostos_json');
        });

    }
};
