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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->enum('status', ['emitido', 'parcialmente_recebido', 'recebido', 'cancelado', 'fechado'])->default('emitido');
            $table->unsignedSmallInteger('supplier_id');
            $table->foreignId('quotation_id')->nullable()->constrained('purchase_quotations')->nullOnDelete();
            $table->date('data_emissao');
            $table->date('data_prevista')->nullable();
            $table->text('observacoes')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('supplier_id')->references('id_fornecedor')->on('fornecedores');
            $table->index(['status', 'data_emissao']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
