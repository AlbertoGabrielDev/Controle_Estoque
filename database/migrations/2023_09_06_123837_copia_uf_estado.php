<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $update = 'update cidade set id_estado_fk = uf';
        DB::statement($update);
    }

    public function down(): void
    {
        $update = 'update cidade set id_estado_fk = null';
        DB::statement($update);
    }
};
