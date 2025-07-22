<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('historico', function(Blueprint $table) {
			$table->unsignedSmallInteger('id_unidade_fk')->nullable();
            $table->foreign('id_unidade_fk')->references('id_unidade')->on('unidades');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('historico', function (Blueprint $table) {
            $table->dropForeign(['id_unidade_fk']);
            $table->dropColumn('id_unidade_fk');
        });
	}
};
