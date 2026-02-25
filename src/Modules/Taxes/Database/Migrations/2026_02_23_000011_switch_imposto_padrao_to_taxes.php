<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function isSqlite(): bool
    {
        return DB::getDriverName() === 'sqlite';
    }

    public function up(): void
    {
        if ($this->isSqlite()) {
            Schema::dropIfExists('impostos');
            return;
        }

        if (Schema::hasTable('clientes') && Schema::hasColumn('clientes', 'imposto_padrao_id')) {
            if (Schema::hasTable('impostos') && Schema::hasTable('taxes')) {
                // Tenta mapear pelo codigo antes de remover a tabela de impostos
                DB::statement(
                    "UPDATE clientes c " .
                    "LEFT JOIN impostos i ON i.id = c.imposto_padrao_id " .
                    "LEFT JOIN taxes t ON t.codigo = i.codigo " .
                    "SET c.imposto_padrao_id = t.id"
                );
            }

            $fkName = $this->foreignKeyName('clientes', 'imposto_padrao_id');
            if ($fkName) {
                DB::statement("ALTER TABLE clientes DROP FOREIGN KEY {$fkName}");
            }

            if (Schema::hasTable('taxes')) {
                Schema::table('clientes', function (Blueprint $table) {
                    $table->foreign('imposto_padrao_id')
                        ->references('id')
                        ->on('taxes')
                        ->nullOnDelete();
                });
            }
        }

        Schema::dropIfExists('impostos');
    }

    public function down(): void
    {
        if ($this->isSqlite()) {
            return;
        }

        if (Schema::hasTable('clientes') && Schema::hasColumn('clientes', 'imposto_padrao_id')) {
            $fkName = $this->foreignKeyName('clientes', 'imposto_padrao_id');
            if ($fkName) {
                DB::statement("ALTER TABLE clientes DROP FOREIGN KEY {$fkName}");
            }

            if (Schema::hasTable('impostos')) {
                Schema::table('clientes', function (Blueprint $table) {
                    $table->foreign('imposto_padrao_id')
                        ->references('id')
                        ->on('impostos')
                        ->nullOnDelete();
                });
            }
        }
    }

    private function foreignKeyName(string $table, string $column): ?string
    {
        if ($this->isSqlite()) {
            return null;
        }

        $schema = DB::getDatabaseName();

        $row = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->select('CONSTRAINT_NAME')
            ->where('TABLE_SCHEMA', $schema)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->first();

        return $row?->CONSTRAINT_NAME;
    }
};
