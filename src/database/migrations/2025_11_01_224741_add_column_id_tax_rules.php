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
        Schema::table('estoques', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tax_fk')->nullable()->after('imposto_total');
            $table->foreign('id_tax_fk')
                ->references('id')
                ->on('tax_rules')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('estoques', function (Blueprint $table) {
            // remove a foreign key e a coluna
            $table->dropForeign(['id_tax_fk']);
            $table->dropColumn('id_tax_fk');
        });
    }
};
