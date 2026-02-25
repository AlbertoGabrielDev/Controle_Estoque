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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('client', 20)->index();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->nullOnDelete();
            $table->enum('status', ['created', 'paid', 'separacao', 'expedido', 'cancelado'])->default('created');
            $table->decimal('total_valor', 10, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
