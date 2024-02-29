<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePontoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ponto', function(Blueprint $table)
		{
			$table->unsignedBigInteger('id', true);
			$table->unsignedBigInteger('utilizador_id')->nullable();
			$table->date('data')->nullable()->unique();
			$table->time('entrada_manha')->nullable();
			$table->time('saida_manha')->nullable();
			$table->time('entrada_tarde')->nullable();
			$table->time('saida_tarde')->nullable();
			$table->smallInteger('status')->nullable();
			$table->timestamps();

            $table->foreign('utilizador_id')->references('id')->on('utilizador');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('ponto', function(Blueprint $table)
        {
            $table->dropForeign('utilizador_id_foreign');
        });
		Schema::drop('ponto');
	}

}
