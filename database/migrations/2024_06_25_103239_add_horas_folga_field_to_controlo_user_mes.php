<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHorasFolgaFieldToControloUserMes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('controlo_user_mes', function (Blueprint $table) {
            $table->integer('horas_folga')->nullable()->default(0)->after('horas_ausencia');
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
            $table->dropColumn('horas_folga');
        });
    }
}
