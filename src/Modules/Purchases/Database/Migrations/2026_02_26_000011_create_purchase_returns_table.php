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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->enum('status', ['aberta', 'confirmada', 'cancelada'])->default('aberta');
            $table->foreignId('receipt_id')->nullable()->constrained('purchase_receipts')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->text('motivo');
            $table->date('data_devolucao');
            $table->timestamps();

            $table->index(['status', 'data_devolucao']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
