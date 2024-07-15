<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationControloUserMesToPonto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ponto', function (Blueprint $table) {
            $table->unsignedBigInteger('controlo_user_mes_id');

            $table->foreign('controlo_user_mes_id')->references('id')->on('controlo_user_mes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ponto', function (Blueprint $table) {
            $table->dropForeign('ponto_controlo_user_mes_id_foriegn');
        });
    }
}
