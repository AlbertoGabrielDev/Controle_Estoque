<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tax_rules', function (Blueprint $table) {
            $table->unsignedBigInteger('produto_id')->nullable()->after('segment_id');


            $table->index('produto_id', 'tax_rules_produto_id_index');
            $table->foreign('produto_id', 'tax_rules_produto_id_fk')
                ->references('id_produto')
                ->on('produtos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tax_rules', function (Blueprint $table) {
            $table->dropForeign('tax_rules_produto_id_fk');
            $table->dropIndex('tax_rules_produto_id_index');
            $table->dropColumn('produto_id');
        });
    }
};
