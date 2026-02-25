<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produtos') && Schema::hasColumn('produtos', 'unidade_medida')) {
            DB::statement('ALTER TABLE `produtos` MODIFY `unidade_medida` VARCHAR(10) NOT NULL');
        }

        if (Schema::hasTable('vendas') && Schema::hasColumn('vendas', 'unidade_medida')) {
            DB::statement('ALTER TABLE `vendas` MODIFY `unidade_medida` VARCHAR(10) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('produtos') && Schema::hasColumn('produtos', 'unidade_medida')) {
            DB::statement('UPDATE `produtos` SET `unidade_medida` = LEFT(`unidade_medida`, 2)');
            DB::statement('ALTER TABLE `produtos` MODIFY `unidade_medida` VARCHAR(2) NOT NULL');
        }

        if (Schema::hasTable('vendas') && Schema::hasColumn('vendas', 'unidade_medida')) {
            DB::statement('UPDATE `vendas` SET `unidade_medida` = LEFT(`unidade_medida`, 2)');
            DB::statement('ALTER TABLE `vendas` MODIFY `unidade_medida` VARCHAR(2) NULL');
        }
    }
};
