<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('estoques')) {
            return;
        }

        Schema::table('estoques', function (Blueprint $table) {
            if (Schema::hasColumn('estoques', 'id_marca_fk')) {
                $table->dropForeign(['id_marca_fk']);
            }
            if (Schema::hasColumn('estoques', 'id_fornecedor_fk')) {
                $table->dropForeign(['id_fornecedor_fk']);
            }
        });

        if (Schema::hasColumn('estoques', 'id_marca_fk')) {
            DB::statement('ALTER TABLE estoques MODIFY id_marca_fk SMALLINT UNSIGNED NULL');
        }
        if (Schema::hasColumn('estoques', 'id_fornecedor_fk')) {
            DB::statement('ALTER TABLE estoques MODIFY id_fornecedor_fk SMALLINT UNSIGNED NULL');
        }

        Schema::table('estoques', function (Blueprint $table) {
            if (Schema::hasColumn('estoques', 'id_marca_fk')) {
                $table->foreign('id_marca_fk')->references('id_marca')->on('marcas');
            }
            if (Schema::hasColumn('estoques', 'id_fornecedor_fk')) {
                $table->foreign('id_fornecedor_fk')->references('id_fornecedor')->on('fornecedores');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('estoques')) {
            return;
        }

        Schema::table('estoques', function (Blueprint $table) {
            if (Schema::hasColumn('estoques', 'id_marca_fk')) {
                $table->dropForeign(['id_marca_fk']);
            }
            if (Schema::hasColumn('estoques', 'id_fornecedor_fk')) {
                $table->dropForeign(['id_fornecedor_fk']);
            }
        });

        if (Schema::hasColumn('estoques', 'id_marca_fk')) {
            DB::statement('ALTER TABLE estoques MODIFY id_marca_fk SMALLINT UNSIGNED NOT NULL');
        }
        if (Schema::hasColumn('estoques', 'id_fornecedor_fk')) {
            DB::statement('ALTER TABLE estoques MODIFY id_fornecedor_fk SMALLINT UNSIGNED NOT NULL');
        }

        Schema::table('estoques', function (Blueprint $table) {
            if (Schema::hasColumn('estoques', 'id_marca_fk')) {
                $table->foreign('id_marca_fk')->references('id_marca')->on('marcas');
            }
            if (Schema::hasColumn('estoques', 'id_fornecedor_fk')) {
                $table->foreign('id_fornecedor_fk')->references('id_fornecedor')->on('fornecedores');
            }
        });
    }
};