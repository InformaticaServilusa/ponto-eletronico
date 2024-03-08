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
			$table->unsignedBigInteger('utilizador_id');
            $table->unsignedBigInteger('tipo_ponto_id');
			$table->date('data')->unique();
			$table->time('entrada_manha')->nullable();
			$table->time('saida_manha')->nullable();
			$table->time('entrada_tarde')->nullable();
			$table->time('saida_tarde')->nullable();
            $table->string('colab_obs')->nullable();
			$table->smallInteger('status')->nullable();
            $table->smallInteger('_ativo')->default(1);
			$table->timestamps();

            $table->foreign('utilizador_id')->references('id')->on('utilizador');
            $table->foreign('tipo_ponto_id')->references('id')->on('tipo_ponto');


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
            $table->dropForeign('ponto_tipo_ponto_id_foreign');
            $table->dropForeign('ponto_utilizador_id_foreign');
        });
		Schema::drop('ponto');
	}

}
