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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('itens');
            $table->string('descricao_snapshot');
            $table->foreignId('unidade_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->decimal('quantidade_pedida', 12, 3);
            $table->decimal('quantidade_recebida', 12, 3)->default(0);
            $table->decimal('preco_unit', 10, 2);
            $table->foreignId('imposto_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->decimal('aliquota_snapshot', 5, 2)->nullable();
            $table->decimal('total_linha', 12, 2);
            $table->timestamps();

            $table->index(['order_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
