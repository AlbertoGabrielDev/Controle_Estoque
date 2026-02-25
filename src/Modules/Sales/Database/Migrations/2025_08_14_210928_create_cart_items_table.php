<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade');
            $table->string('cod_produto', 60)->index(); // SKU
            $table->string('nome_produto', 60);
            $table->decimal('preco_unit', 10, 2);
            $table->unsignedInteger('quantidade');
            $table->decimal('subtotal_valor', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
