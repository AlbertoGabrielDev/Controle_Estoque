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
        Schema::create('purchase_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained('purchase_receipts')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('purchase_order_items')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('itens');
            $table->decimal('quantidade_recebida', 12, 3);
            $table->decimal('preco_unit_recebido', 10, 2);
            $table->foreignId('imposto_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->decimal('aliquota_snapshot', 5, 2)->nullable();
            $table->boolean('divergencia_flag')->default(false);
            $table->text('motivo_divergencia')->nullable();
            $table->timestamps();

            $table->index(['receipt_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receipt_items');
    }
};
