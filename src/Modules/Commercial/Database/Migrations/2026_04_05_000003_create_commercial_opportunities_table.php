<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('commercial_opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->string('nome', 200);
            $table->text('descricao')->nullable();
            $table->string('origem', 100)->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'novo',
                'em_contato',
                'proposta_enviada',
                'negociacao',
                'ganho',
                'perdido',
            ])->default('novo');
            $table->decimal('valor_estimado', 12, 2)->default(0);
            $table->date('data_prevista_fechamento')->nullable();
            $table->text('motivo_perda')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id_cliente')->on('clientes')->nullOnDelete();
            $table->index(['status', 'data_prevista_fechamento']);
            $table->index(['cliente_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_opportunities');
    }
};
