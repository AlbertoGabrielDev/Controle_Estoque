<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contas_contabeis', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nome', 120);
            $table->enum('tipo', ['ativo', 'passivo', 'receita', 'despesa', 'patrimonio'])->default('ativo');
            $table->unsignedBigInteger('conta_pai_id')->nullable();
            $table->boolean('aceita_lancamento')->default(true);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->foreign('conta_pai_id')
                ->references('id')
                ->on('contas_contabeis')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_contabeis');
    }
};
