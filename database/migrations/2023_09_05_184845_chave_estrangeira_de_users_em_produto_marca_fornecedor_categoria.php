<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('marca', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');
        });

        Schema::table('produto', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');
        });

        Schema::table('fornecedor', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');
        });

        Schema::table('categoria', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fornecedor', function (Blueprint $table) {
            $table->dropForeign('id_users_fk');
            $table->dropColumn('id_users_fk')->references('id')->on('users');
        });
    
        Schema::table('marca', function (Blueprint $table) {
            $table->dropForeign('id_users_fk');
            $table->dropColumn('id_users_fk')->references('id')->on('users');
        });

        Schema::table('produto', function (Blueprint $table) {
                $table->dropForeign('id_users_fk');
                $table->dropColumn('id_users_fk')->references('id')->on('users');
        });
        Schema::table('categoria', function (Blueprint $table) {
            $table->dropForeign('id_users_fk');
            $table->dropColumn('id_users_fk')->references('id')->on('users');
        });
}
};
