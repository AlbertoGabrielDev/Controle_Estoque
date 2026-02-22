<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('descricao', 120);
            $table->decimal('fator_base', 12, 6)->default(1);
            $table->unsignedBigInteger('unidade_base_id')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->foreign('unidade_base_id')
                ->references('id')
                ->on('unidades_medida')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_medida');
    }
};
