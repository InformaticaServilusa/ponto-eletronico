<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAusencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ausencia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('utilizador_id');
            $table->unsignedBigInteger('tipo_ausencia_id');
            $table->string('obs_colab')->nullable();
            $table->string('obs_coord')->nullable();
            $table->date('data');
            $table->string('hora_inicio')->nullable();
            $table->string('hora_fim')->nullable();
            $table->smallInteger('status')->nullable();
            $table->smallInteger('_ativo')->default(1);
            $table->integer('horas_ausencia')->nullable();
            $table->string('anexo', 100)->nullable();
            $table->timestamps();

            $table->foreign('utilizador_id')->references('id')->on('utilizador');
            $table->foreign('tipo_ausencia_id')->references('id')->on('tipo_ausencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ausencia', function(Blueprint $table)
        {
            $table->dropForeign('ausencia_tipo_ausencia_id_foreign');
            $table->dropForeign('ausencia_utilizador_id_foreign');
        });
        Schema::dropIfExists('table_ausencia');
    }
}
