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
        Schema::create('commercial_discount_policies', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->enum('tipo', ['item', 'pedido'])->default('item');
            $table->decimal('percentual_maximo', 5, 2)->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['tipo', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_discount_policies');
    }
};
