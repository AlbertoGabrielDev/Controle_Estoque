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
        Schema::create('purchase_quotation_suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('purchase_quotations')->cascadeOnDelete();
            $table->unsignedSmallInteger('supplier_id');
            $table->enum('status', ['convidado', 'respondeu', 'recusou'])->default('convidado');
            $table->unsignedInteger('prazo_entrega_dias')->nullable();
            $table->string('condicao_pagamento', 120)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id_fornecedor')->on('fornecedores');
            $table->unique(['quotation_id', 'supplier_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_quotation_suppliers');
    }
};
