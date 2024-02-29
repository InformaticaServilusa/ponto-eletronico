<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsuarioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('utilizador', function(Blueprint $table)
		{
			$table->unsignedBigInteger('id', true);
			$table->string('nome', 100)->nullable();
			$table->string('guuID', 100)->unique();
			$table->string('email', 100)->nullable();
			$table->string('cargo', 100)->nullable();
			$table->string('regime', 100)->nullable();
			$table->string('departamento', 100)->nullable();
			$table->string('local', 100)->nullable();
			$table->unsignedBigInteger('coordenador_id')->nullable();
			$table->smallInteger('_admin')->default(0);
			$table->smallInteger('_coodenador')->default(0);
			$table->smallInteger('_dep_rh')->default(0);
			$table->smallInteger('_ativo')->default(1);
			$table->timestamps();

            $table->foreign('coordenador_id')->references('id')->on('utilizador');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('utilizador', function(Blueprint $table)
        {
            $table->dropForeign('coordenador_id_foreign');
        });
		Schema::drop('usuario');
	}

}
