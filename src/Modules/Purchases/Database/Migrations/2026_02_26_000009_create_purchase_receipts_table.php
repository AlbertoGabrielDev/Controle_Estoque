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
        Schema::create('purchase_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->enum('status', ['registrado', 'conferido', 'com_divergencia', 'estornado'])->default('registrado');
            $table->foreignId('order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->unsignedSmallInteger('supplier_id');
            $table->date('data_recebimento');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id_fornecedor')->on('fornecedores');
            $table->index(['status', 'data_recebimento']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receipts');
    }
};
