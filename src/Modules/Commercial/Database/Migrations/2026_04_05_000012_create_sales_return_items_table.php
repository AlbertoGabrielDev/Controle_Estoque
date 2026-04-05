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
        Schema::create('commercial_sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('commercial_sales_returns')->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained('commercial_sales_invoice_items')->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('commercial_sales_order_items')->nullOnDelete();
            $table->foreignId('item_id')->constrained('itens');
            $table->decimal('quantidade_devolvida', 12, 3);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index('return_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_sales_return_items');
    }
};
