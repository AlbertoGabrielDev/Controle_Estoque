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
        Schema::create('purchase_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->enum('status', ['aberta', 'encerrada', 'cancelada'])->default('aberta');
            $table->foreignId('requisition_id')->constrained('purchase_requisitions')->cascadeOnDelete();
            $table->date('data_limite')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['status', 'data_limite']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_quotations');
    }
};
