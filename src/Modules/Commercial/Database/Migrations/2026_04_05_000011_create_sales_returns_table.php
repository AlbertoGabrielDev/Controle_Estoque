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
        Schema::create('commercial_sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('invoice_id')->nullable()->constrained('commercial_sales_invoices')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('commercial_sales_orders')->nullOnDelete();
            $table->unsignedBigInteger('cliente_id');
            $table->enum('status', ['aberta', 'confirmada', 'cancelada'])->default('aberta');
            $table->text('motivo');
            $table->date('data_devolucao');
            $table->timestamps();

            $table->foreign('cliente_id')->references('id_cliente')->on('clientes');
            $table->index(['status', 'data_devolucao']);
            $table->index(['cliente_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_sales_returns');
    }
};
