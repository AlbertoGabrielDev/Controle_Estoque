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
        Schema::create('commercial_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('opportunity_id')->nullable()->constrained('commercial_opportunities')->nullOnDelete();
            $table->unsignedBigInteger('cliente_id');
            $table->enum('status', [
                'rascunho',
                'enviada',
                'aprovada',
                'rejeitada',
                'vencida',
                'convertida',
            ])->default('rascunho');
            $table->date('data_emissao');
            $table->date('validade_ate')->nullable();
            $table->text('observacoes')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('desconto_total', 12, 2)->default(0);
            $table->decimal('total_impostos', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('cliente_id')->references('id_cliente')->on('clientes');
            $table->index(['status', 'data_emissao']);
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
        Schema::dropIfExists('commercial_proposals');
    }
};
