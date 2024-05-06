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
			$table->date('data');
			$table->string('entrada_manha')->nullable();
			$table->string('saida_manha')->nullable();
			$table->string('entrada_tarde')->nullable();
			$table->string('saida_tarde')->nullable();
			$table->string('entrada_noite')->nullable();
			$table->string('saida_noite')->nullable();
            $table->string('obs_colab')->nullable();
            $table->string('obs_coord')->nullable();
			$table->smallInteger('status')->nullable();
            $table->smallInteger('_ativo')->default(1);
            $table->index(['utilizador_id','data']);
            $table->integer('total_horas_trabalhadas')->nullable();
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
