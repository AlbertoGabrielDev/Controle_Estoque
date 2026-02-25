<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('tax_rules', function (Blueprint $table) {
            $table->unsignedTinyInteger('escopo')->default(1)->after('tax_id');
            $table->index('escopo');
        });
    }

    public function down(): void
    {
        Schema::table('tax_rules', function (Blueprint $table) {
            $table->dropColumn('escopo');
            $table->dropIndex(['escopo']);
        });
    }
};
