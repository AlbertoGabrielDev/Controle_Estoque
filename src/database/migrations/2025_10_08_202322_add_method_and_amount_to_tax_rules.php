<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tax_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('tax_rules', 'metodo')) {
                // 1=percent, 2=fixed, 3=formula
                $table->unsignedTinyInteger('metodo')->default(1)->after('base_formula');
                $table->index('metodo');
            }
            if (!Schema::hasColumn('tax_rules', 'valor_fixo')) {
                $table->decimal('valor_fixo', 12, 2)->nullable()->after('aliquota_percent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_rules', function (Blueprint $table) {
            if (Schema::hasColumn('tax_rules', 'metodo')) {
                $table->dropIndex(['metodo']);
                $table->dropColumn('metodo');
            }
            if (Schema::hasColumn('tax_rules', 'valor_fixo')) {
                $table->dropColumn('valor_fixo');
            }
        });
    }
};
