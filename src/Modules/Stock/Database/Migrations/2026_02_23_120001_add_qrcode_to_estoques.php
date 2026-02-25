<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('estoques')) {
            return;
        }

        Schema::table('estoques', function (Blueprint $table) {
            if (!Schema::hasColumn('estoques', 'qrcode')) {
                $table->string('qrcode', 80)->nullable()->unique('estoques_qrcode_unique')->after('status');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('estoques')) {
            return;
        }

        Schema::table('estoques', function (Blueprint $table) {
            if (Schema::hasColumn('estoques', 'qrcode')) {
                $table->dropUnique('estoques_qrcode_unique');
                $table->dropColumn('qrcode');
            }
        });
    }
};
