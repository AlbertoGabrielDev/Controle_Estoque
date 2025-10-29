<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabela de segmentos
        if (!Schema::hasTable('customer_segments')) {
            Schema::create('customer_segments', function (Blueprint $table) {
                $table->id();
                $table->string('nome')->unique(); // ex.: Varejo, Atacado, Revendedor
                $table->timestamps();
            });
        }

        // SEGMENTO NO CLIENTE (só aplica se a tabela existir)
        if (Schema::hasTable('clientes') && !Schema::hasColumn('clientes', 'segment_id')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->foreignId('segment_id')->nullable()->after('id')->constrained('customer_segments')->nullOnDelete();
                // Se sua PK de clientes não é 'id', tudo bem — o FK aqui é só para integridade.
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('clientes') && Schema::hasColumn('clientes', 'segment_id')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropConstrainedForeignId('segment_id');
            });
        }
        Schema::dropIfExists('customer_segments');
    }
};
