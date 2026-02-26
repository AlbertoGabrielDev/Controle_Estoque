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
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->enum('status', ['draft', 'aprovado', 'cancelado', 'fechado'])->default('draft');
            $table->foreignId('solicitado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacoes')->nullable();
            $table->date('data_requisicao')->nullable();
            $table->timestamps();

            $table->index(['status', 'data_requisicao']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};
