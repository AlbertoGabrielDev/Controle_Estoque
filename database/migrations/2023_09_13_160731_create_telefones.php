<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telefones', function (Blueprint $table) {
            $table->smallIncrements('id_telefone');
            $table->string('ddd',2);
            $table->string('telefone',100);
            $table->boolean('whatsapp')->default(0);
            $table->boolean('telegram')->default(0);
            $table->boolean('principal')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telefones');
    }
};
