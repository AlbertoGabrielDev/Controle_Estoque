<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Helper: cria índice apenas se não existir (MySQL) */
    private function ensureIndex(string $table, string $indexName, array $cols): void
    {
        $db = DB::getDatabaseName();
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $db)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $indexName)
            ->exists();

        if (!$exists) {
            Schema::table($table, function (Blueprint $t) use ($indexName, $cols) {
                $t->index($cols, $indexName);
            });
        }
    }

    /** Helper: dropar índice se existir (MySQL) */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        $db = DB::getDatabaseName();
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $db)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $indexName)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
        }
    }

    /** Helper: dropar FK pelo nome se existir (MySQL) */
    private function dropForeignIfExists(string $table, string $fkName): void
    {
        $db = DB::getDatabaseName();
        $exists = DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $db)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $fkName)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fkName}`");
        }
    }

    /** Helper: dropar FK(s) que usem exatamente as colunas informadas (na ordem) */
    private function dropFksByExactColumns(string $table, array $cols): void
    {
        $db = DB::getDatabaseName();
        $want = implode(',', $cols);

        $rows = DB::select("
            SELECT kcu.CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE kcu
            WHERE kcu.TABLE_SCHEMA = ?
              AND kcu.TABLE_NAME = ?
              AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
            GROUP BY kcu.CONSTRAINT_NAME
            HAVING GROUP_CONCAT(kcu.COLUMN_NAME ORDER BY kcu.ORDINAL_POSITION SEPARATOR ',') = ?
        ", [$db, $table, $want]);

        foreach ($rows as $r) {
            $this->dropForeignIfExists($table, $r->CONSTRAINT_NAME);
        }
    }

    public function up(): void
    {
        // 0) Garante índices simples que outras FKs possam usar (sem duplicar)
        if (Schema::hasTable('tax_rules')) {
            if (Schema::hasColumn('tax_rules', 'segment_id')) {
                $this->ensureIndex('tax_rules', 'tax_rules_segment_id_idx', ['segment_id']);
            }
            if (Schema::hasColumn('tax_rules', 'ncm_padrao')) {
                $this->ensureIndex('tax_rules', 'tax_rules_ncm_padrao_idx', ['ncm_padrao']);
            }
            if (Schema::hasColumn('tax_rules', 'prioridade')) {
                $this->ensureIndex('tax_rules', 'tax_rules_prioridade_index', ['prioridade']);
            }
            if (Schema::hasColumn('tax_rules', 'metodo')) {
                $this->ensureIndex('tax_rules', 'tax_rules_metodo_index', ['metodo']);
            }
            if (Schema::hasColumn('tax_rules', 'escopo')) {
                $this->ensureIndex('tax_rules', 'tax_rules_escopo_index', ['escopo']);
            }
            if (Schema::hasColumn('tax_rules', 'vigencia_inicio') && Schema::hasColumn('tax_rules', 'vigencia_fim')) {
                $this->ensureIndex('tax_rules', 'tax_rules_vigencia_inicio_vigencia_fim_index', ['vigencia_inicio', 'vigencia_fim']);
            }
            if (
                Schema::hasColumn('tax_rules', 'uf_origem') &&
                Schema::hasColumn('tax_rules', 'uf_destino') &&
                Schema::hasColumn('tax_rules', 'canal') &&
                Schema::hasColumn('tax_rules', 'tipo_operacao')
            ) {
                $this->ensureIndex(
                    'tax_rules',
                    'tax_rules_uf_origem_uf_destino_canal_tipo_operacao_index',
                    ['uf_origem','uf_destino','canal','tipo_operacao']
                );
            }
        }

        // 1) Remove FKs que possam estar “presas” ao índice composto antigo
        // (ajuste as colunas conforme seu esquema antigo — aqui deixei um exemplo comum)
        $this->dropFksByExactColumns('tax_rules', ['segment_id','categoria_produto_id','ncm_padrao']);

        // 2) Tenta derrubar o índice composto antigo se existir
        $this->dropIndexIfExists('tax_rules', 'tax_rules_segment_id_categoria_produto_id_ncm_padrao_index');

        // 3) (Se existir) derruba FK simples automática em categoria_produto_id
        //    Obs.: o nome pode variar. Primeiro tenta pelo Laravel padrão.
        if (Schema::hasColumn('tax_rules', 'categoria_produto_id')) {
            $this->dropForeignIfExists('tax_rules', 'tax_rules_categoria_produto_id_foreign');
            // Se foi criada com outro nome, tente de novo via Schema::table (não quebra se não existir)
            try {
                Schema::table('tax_rules', function (Blueprint $table) {
                    $table->dropForeign(['categoria_produto_id']);
                });
            } catch (\Throwable $e) {
                // ok
            }
        }

        // 4) Cria a tabela de alvos (categoria e/ou produto) para N:N com tax_rules
        if (!Schema::hasTable('tax_rule_alvos')) {
            Schema::create('tax_rule_alvos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tax_rule_id')->constrained('tax_rules')->cascadeOnDelete();

                $table->unsignedSmallInteger('id_categoria_fk')->nullable();
                $table->unsignedSmallInteger('id_produto_fk')->nullable();

                $table->timestamps();

                $table->unique(['tax_rule_id', 'id_categoria_fk'], 'tra_rule_cat_unique');
                $table->unique(['tax_rule_id', 'id_produto_fk'], 'tra_rule_prod_unique');

                // FKs explícitas
                $table->foreign('id_categoria_fk')->references('id_categoria')->on('categorias')->cascadeOnDelete();
                $table->foreign('id_produto_fk')->references('id_produto')->on('produtos')->cascadeOnDelete();

                // Índices para buscas rápidas
                $table->index(['id_categoria_fk'], 'tra_cat_idx');
                $table->index(['id_produto_fk'], 'tra_prod_idx');
            });
        }

        // 5) Backfill: copia a categoria antiga (se a coluna ainda existir)
        if (Schema::hasColumn('tax_rules', 'id_categoria_fk')) {
            DB::statement("
                INSERT INTO tax_rule_alvos (tax_rule_id, id_categoria_fk, created_at, updated_at)
                SELECT id, id_categoria_fk, NOW(), NOW()
                FROM tax_rules
                WHERE id_categoria_fk IS NOT NULL
            ");
        }

        // 6) Remove a coluna antiga e quaisquer índices remanescentes sobre ela
        if (Schema::hasColumn('tax_rules', 'id_categoria_fk')) {
            // Tenta derrubar algum índice remanescente que envolva a coluna
            $this->dropIndexIfExists('tax_rules', 'tax_rules_segment_id_id_categoria_fk_ncm_padrao_index');
            $this->dropIndexIfExists('tax_rules', 'tax_rules_id_categoria_fk_index');

            Schema::table('tax_rules', function (Blueprint $table) {
                $table->dropColumn('id_categoria_fk');
            });
        }

        // 7) Reconfirma/garante os índices “bons” (sem duplicar)
        if (Schema::hasTable('tax_rules')) {
            $this->ensureIndex('tax_rules', 'tax_rules_segment_id_idx', ['segment_id']);
            $this->ensureIndex('tax_rules', 'tax_rules_ncm_padrao_idx', ['ncm_padrao']);
            $this->ensureIndex('tax_rules', 'tax_rules_prioridade_index', ['prioridade']);
            $this->ensureIndex('tax_rules', 'tax_rules_metodo_index', ['metodo']);
            $this->ensureIndex('tax_rules', 'tax_rules_escopo_index', ['escopo']);
            $this->ensureIndex('tax_rules', 'tax_rules_vigencia_inicio_vigencia_fim_index', ['vigencia_inicio','vigencia_fim']);
            $this->ensureIndex(
                'tax_rules',
                'tax_rules_uf_origem_uf_destino_canal_tipo_operacao_index',
                ['uf_origem','uf_destino','canal','tipo_operacao']
            );
            // se ainda existir categoria_produto_id (em alguns bancos), garanta índice simples
            if (Schema::hasColumn('tax_rules', 'categoria_produto_id')) {
                $this->ensureIndex('tax_rules', 'tax_rules_categoria_produto_id_idx', ['categoria_produto_id']);
            }
        }
    }

    public function down(): void
    {
        // 1) Recria coluna antiga (fallback) — não assume FK aqui
        if (!Schema::hasColumn('tax_rules', 'id_categoria_fk')) {
            Schema::table('tax_rules', function (Blueprint $table) {
                $table->unsignedBigInteger('id_categoria_fk')->nullable()->after('segment_id');
            });
        }

        // 2) Restaura um valor (primeira categoria por regra) a partir de tax_rule_alvos
        if (Schema::hasTable('tax_rule_alvos')) {
            DB::statement("
                UPDATE tax_rules tr
                JOIN (
                    SELECT tax_rule_id, MIN(id_categoria_fk) AS cat
                    FROM tax_rule_alvos
                    WHERE id_categoria_fk IS NOT NULL
                    GROUP BY tax_rule_id
                ) x ON x.tax_rule_id = tr.id
                SET tr.id_categoria_fk = x.cat
            ");
        }

        // 3) (Opcional) recria índice composto antigo caso você realmente precise voltar ao estado anterior
        //    — Se não precisar, pode remover este bloco.
        $this->ensureIndex('tax_rules', 'tax_rules_segment_id_idx', ['segment_id']);
        $this->ensureIndex('tax_rules', 'tax_rules_ncm_padrao_idx', ['ncm_padrao']);
        // Caso queira reconstituir o composto antigo (ajuste os nomes/colunas conforme seu histórico):
        // $this->ensureIndex('tax_rules', 'tax_rules_segment_id_categoria_produto_id_ncm_padrao_index', ['segment_id','categoria_produto_id','ncm_padrao']);

        // 4) Dropar a tabela nova
        Schema::dropIfExists('tax_rule_alvos');
    }
};
