<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tax_rules', function (Blueprint $table) {
            $table->unsignedSmallInteger('produto_id_fk')->nullable()->after('segment_id');
            $table->foreign('produto_id_fk')
                ->references('id_produto')
                ->on('produtos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tax_rules', function (Blueprint $table) {
            $table->dropForeign('produto_id_fk');
            $table->dropColumn('produto_id_fk');
        });
    }
};
