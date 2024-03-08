<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horario', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->time('entrada_manha');
            $table->time('saida_manha')->nullable();
            $table->time('entrada_tarde')->nullable();
            $table->time('saida_tarde')->nullable();
            $table->time('entrada_noite')->nullable();
            $table->time('saida_noite')->nullable();
            $table->unsignedBigInteger('regime_id');
            $table->integer('horas_semanais');
            $table->integer('horas_diarias');
            $table->integer('dias_max_seguidos');
            $table->timestamps();

            $table->foreign('regime_id')->references('id')->on('regime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('horario', function(Blueprint $table)
        {

            $table->dropForeign('horario_regime_id_foreign');
        });

        Schema::dropIfExists('horario');
    }
}
