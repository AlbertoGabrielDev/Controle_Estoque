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
        Schema::create('purchase_quotation_supplier_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_supplier_id')->constrained('purchase_quotation_suppliers')->cascadeOnDelete();
            $table->foreignId('requisition_item_id')->constrained('purchase_requisition_items')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('itens');
            $table->decimal('quantidade', 12, 3);
            $table->decimal('preco_unit', 10, 2);
            $table->foreignId('imposto_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->decimal('aliquota_snapshot', 5, 2)->nullable();
            $table->boolean('selecionado')->default(false);
            $table->timestamps();

            $table->unique(['quotation_supplier_id', 'requisition_item_id'], 'pqs_items_qs_req_uq');
            $table->index(['item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_quotation_supplier_items');
    }
};
