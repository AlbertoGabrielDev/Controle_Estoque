<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            if (!Schema::hasColumn('categorias', 'codigo')) {
                $table->string('codigo', 30)->unique()->nullable()->after('id_categoria');
            }
            if (!Schema::hasColumn('categorias', 'tipo')) {
                $table->enum('tipo', ['produto', 'cliente', 'fornecedor'])->default('produto')->after('codigo');
            }
            if (!Schema::hasColumn('categorias', 'categoria_pai_id')) {
                $table->unsignedSmallInteger('categoria_pai_id')->nullable()->after('tipo');
            }
            if (!Schema::hasColumn('categorias', 'ativo')) {
                $table->boolean('ativo')->default(true)->after('status');
            }
        });

        Schema::table('categorias', function (Blueprint $table) {
            if (Schema::hasColumn('categorias', 'categoria_pai_id')) {
                $table->foreign('categoria_pai_id')
                    ->references('id_categoria')
                    ->on('categorias')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            if (Schema::hasColumn('categorias', 'categoria_pai_id')) {
                $table->dropForeign(['categoria_pai_id']);
                $table->dropColumn('categoria_pai_id');
            }
            if (Schema::hasColumn('categorias', 'ativo')) {
                $table->dropColumn('ativo');
            }
            if (Schema::hasColumn('categorias', 'tipo')) {
                $table->dropColumn('tipo');
            }
            if (Schema::hasColumn('categorias', 'codigo')) {
                $table->dropColumn('codigo');
            }
        });
    }
};
