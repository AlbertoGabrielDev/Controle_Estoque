<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('centros_custo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nome', 120);
            $table->unsignedBigInteger('centro_pai_id')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->foreign('centro_pai_id')
                ->references('id')
                ->on('centros_custo')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('centros_custo');
    }
};
