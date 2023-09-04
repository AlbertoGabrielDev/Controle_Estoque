<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fornecedor', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('fornecedor', function (Blueprint $table) {
            $table->dropForeign('id_users_fk');
            $table->dropColumn('id_users_fk')->references('id')->on('users');
        });
    }
};
