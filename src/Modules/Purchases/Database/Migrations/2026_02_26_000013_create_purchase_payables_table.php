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
        Schema::create('purchase_payables', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('supplier_id');
            $table->foreignId('order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('receipt_id')->nullable()->constrained('purchase_receipts')->nullOnDelete();
            $table->string('numero_documento', 60);
            $table->date('data_emissao');
            $table->date('data_vencimento');
            $table->decimal('valor_total', 12, 2);
            $table->enum('status', ['aberto', 'pago', 'cancelado'])->default('aberto');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id_fornecedor')->on('fornecedores');
            $table->index(['status', 'data_vencimento']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payables');
    }
};
