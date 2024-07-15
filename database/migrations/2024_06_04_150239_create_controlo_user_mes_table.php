<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControloUserMesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controlo_user_mes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('utilizador_id');
            $table->char('ano_mes', 7);
            $table->integer('horas_trabalhadas')->nullable()->default(0);
            $table->integer('feriados_trabalhados')->nullable()->default(0);
            $table->integer('horas_ausencia')->nullable()->default(0);
            $table->integer('ferias')->nullable()->default(0);
            $table->boolean('_processado')->default(0);
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
        Schema::table('controlo_user_mes', function(Blueprint $table)
        {
            $table->dropForeign('controlo_user_mes_utilizador_id_foreign');
        });
        Schema::dropIfExists('controlo_user_mes');
    }
}
