<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'codigo')) {
                $table->string('codigo', 30)->unique()->nullable()->after('id_cliente');
            }
            if (!Schema::hasColumn('clientes', 'nif_cif')) {
                $table->string('nif_cif', 40)->nullable()->after('nome_fantasia');
            }
            if (!Schema::hasColumn('clientes', 'endereco_faturacao')) {
                $table->text('endereco_faturacao')->nullable()->after('pais');
            }
            if (!Schema::hasColumn('clientes', 'endereco_entrega')) {
                $table->text('endereco_entrega')->nullable()->after('endereco_faturacao');
            }
            if (!Schema::hasColumn('clientes', 'condicao_pagamento')) {
                $table->string('condicao_pagamento', 60)->nullable()->after('tabela_preco');
            }
            if (!Schema::hasColumn('clientes', 'tabela_preco_id')) {
                $table->unsignedBigInteger('tabela_preco_id')->nullable()->after('condicao_pagamento');
            }
            if (!Schema::hasColumn('clientes', 'imposto_padrao_id')) {
                $table->unsignedBigInteger('imposto_padrao_id')->nullable()->after('tabela_preco_id');
            }
            if (!Schema::hasColumn('clientes', 'ativo')) {
                $table->boolean('ativo')->default(true)->after('status');
            }
        });

        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'tabela_preco_id')) {
                $table->foreign('tabela_preco_id')
                    ->references('id')
                    ->on('tabelas_preco')
                    ->nullOnDelete();
            }
            if (Schema::hasColumn('clientes', 'imposto_padrao_id')) {
                $table->foreign('imposto_padrao_id')
                    ->references('id')
                    ->on('impostos')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'imposto_padrao_id')) {
                $table->dropForeign(['imposto_padrao_id']);
                $table->dropColumn('imposto_padrao_id');
            }
            if (Schema::hasColumn('clientes', 'tabela_preco_id')) {
                $table->dropForeign(['tabela_preco_id']);
                $table->dropColumn('tabela_preco_id');
            }
            if (Schema::hasColumn('clientes', 'ativo')) {
                $table->dropColumn('ativo');
            }
            if (Schema::hasColumn('clientes', 'condicao_pagamento')) {
                $table->dropColumn('condicao_pagamento');
            }
            if (Schema::hasColumn('clientes', 'endereco_entrega')) {
                $table->dropColumn('endereco_entrega');
            }
            if (Schema::hasColumn('clientes', 'endereco_faturacao')) {
                $table->dropColumn('endereco_faturacao');
            }
            if (Schema::hasColumn('clientes', 'nif_cif')) {
                $table->dropColumn('nif_cif');
            }
            if (Schema::hasColumn('clientes', 'codigo')) {
                $table->dropColumn('codigo');
            }
        });
    }
};
