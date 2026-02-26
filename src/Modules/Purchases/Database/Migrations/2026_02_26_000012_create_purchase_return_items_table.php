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
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('purchase_returns')->cascadeOnDelete();
            $table->foreignId('receipt_item_id')->nullable()->constrained('purchase_receipt_items')->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('purchase_order_items')->nullOnDelete();
            $table->foreignId('item_id')->constrained('itens');
            $table->decimal('quantidade_devolvida', 12, 3);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['return_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
    }
};
