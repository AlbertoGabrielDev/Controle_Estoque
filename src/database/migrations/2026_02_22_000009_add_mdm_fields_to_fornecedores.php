<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fornecedores', function (Blueprint $table) {
            if (!Schema::hasColumn('fornecedores', 'codigo')) {
                $table->string('codigo', 30)->unique()->nullable()->after('id_fornecedor');
            }
            if (!Schema::hasColumn('fornecedores', 'razao_social')) {
                $table->string('razao_social', 120)->nullable()->after('codigo');
            }
            if (!Schema::hasColumn('fornecedores', 'nif_cif')) {
                $table->string('nif_cif', 40)->nullable()->after('cnpj');
            }
            if (!Schema::hasColumn('fornecedores', 'endereco')) {
                $table->text('endereco')->nullable()->after('uf');
            }
            if (!Schema::hasColumn('fornecedores', 'prazo_entrega_dias')) {
                $table->integer('prazo_entrega_dias')->default(0)->after('endereco');
            }
            if (!Schema::hasColumn('fornecedores', 'condicao_pagamento')) {
                $table->string('condicao_pagamento', 60)->nullable()->after('prazo_entrega_dias');
            }
            if (!Schema::hasColumn('fornecedores', 'ativo')) {
                $table->boolean('ativo')->default(true)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fornecedores', function (Blueprint $table) {
            if (Schema::hasColumn('fornecedores', 'ativo')) {
                $table->dropColumn('ativo');
            }
            if (Schema::hasColumn('fornecedores', 'condicao_pagamento')) {
                $table->dropColumn('condicao_pagamento');
            }
            if (Schema::hasColumn('fornecedores', 'prazo_entrega_dias')) {
                $table->dropColumn('prazo_entrega_dias');
            }
            if (Schema::hasColumn('fornecedores', 'endereco')) {
                $table->dropColumn('endereco');
            }
            if (Schema::hasColumn('fornecedores', 'nif_cif')) {
                $table->dropColumn('nif_cif');
            }
            if (Schema::hasColumn('fornecedores', 'razao_social')) {
                $table->dropColumn('razao_social');
            }
            if (Schema::hasColumn('fornecedores', 'codigo')) {
                $table->dropColumn('codigo');
            }
        });
    }
};
