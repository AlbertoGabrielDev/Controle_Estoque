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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('client', 20)->index(); // telefone do WhatsApp
            $table->enum('status', ['open', 'ordered', 'abandoned'])->default('open');
            $table->decimal('total_valor', 10, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
