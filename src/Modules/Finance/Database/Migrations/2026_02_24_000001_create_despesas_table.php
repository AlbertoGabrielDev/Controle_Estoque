<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->string('descricao', 255);
            $table->decimal('valor', 12, 2);
            $table->unsignedBigInteger('centro_custo_id')->nullable();
            $table->unsignedBigInteger('conta_contabil_id')->nullable();
            $table->unsignedSmallInteger('fornecedor_id')->nullable();
            $table->string('documento', 60)->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->foreign('centro_custo_id')
                ->references('id')
                ->on('centros_custo')
                ->nullOnDelete();

            $table->foreign('conta_contabil_id')
                ->references('id')
                ->on('contas_contabeis')
                ->nullOnDelete();

            $table->foreign('fornecedor_id')
                ->references('id_fornecedor')
                ->on('fornecedores')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('despesas');
    }
};
