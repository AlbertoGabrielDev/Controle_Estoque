<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            // adiciona a coluna depois do id_produto_fk (opcional)
            $table->unsignedSmallInteger('id_estoque_fk')->nullable()->after('id_produto_fk');

            // cria a chave estrangeira referenciando estoques.id_estoque
            $table->foreign('id_estoque_fk')
                ->references('id_estoque')
                ->on('estoques')
                ->onDelete('set null'); // se o estoque for deletado, zera o campo na venda
        });
    }

    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            // remove a foreign key e a coluna
            $table->dropForeign(['id_estoque_fk']);
            $table->dropColumn('id_estoque_fk');
        });
    }
};
