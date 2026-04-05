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
        Schema::create('commercial_sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('commercial_sales_invoices')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('commercial_sales_order_items');
            $table->foreignId('item_id')->constrained('itens');
            $table->string('descricao_snapshot', 255);
            $table->decimal('quantidade_faturada', 12, 3);
            $table->decimal('preco_unit', 12, 2);
            $table->decimal('desconto_percent', 5, 2)->default(0);
            $table->decimal('desconto_valor', 12, 2)->default(0);
            $table->foreignId('imposto_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->decimal('aliquota_snapshot', 5, 2)->nullable();
            $table->decimal('total_linha', 12, 2);
            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_sales_invoice_items');
    }
};
