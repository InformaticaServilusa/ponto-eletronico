<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFolgasTrabalhasToControloUserMes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('controlo_user_mes', function (Blueprint $table) {
            $table->integer('folgas_trabalhadas')->default(0)->after('horas_folga');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('controlo_user_mes', function (Blueprint $table) {
            $table->dropColumn('folgas_trabalhadas');
        });
    }
}
