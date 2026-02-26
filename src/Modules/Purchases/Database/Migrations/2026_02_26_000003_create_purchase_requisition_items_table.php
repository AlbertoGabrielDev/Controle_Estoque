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
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('purchase_requisitions')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('itens');
            $table->string('descricao_snapshot');
            $table->foreignId('unidade_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->decimal('quantidade', 12, 3);
            $table->decimal('preco_estimado', 10, 2)->default(0);
            $table->foreignId('imposto_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['requisition_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
};
