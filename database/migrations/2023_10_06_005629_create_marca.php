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
        Schema::create('marca', function (Blueprint $table) {
            $table->smallIncrements('id_marca');
            $table->string('nome_marca',20);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->unsignedSmallInteger('id_users_fk');
            $table->foreign('id_users_fk')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marca');
    }
};
