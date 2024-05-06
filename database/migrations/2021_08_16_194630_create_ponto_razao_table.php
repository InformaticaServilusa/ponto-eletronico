<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePontoRazaoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ponto_ajuste_razao', function(Blueprint $table)
		{
			$table->unsignedBigInteger('id', true);
			$table->string('descricao', 100)->nullable();
			$table->smallInteger('ativo')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ponto_ajuste_razao');
	}

}
