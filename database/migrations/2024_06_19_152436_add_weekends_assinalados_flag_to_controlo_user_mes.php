<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeekendsAssinaladosFlagToControloUserMes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('controlo_user_mes', function (Blueprint $table) {
            $table->boolean('weekends_marked')->default(false)->after('_processado');
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
            $table->dropColumn('weekends_marked');
        });
    }
}
