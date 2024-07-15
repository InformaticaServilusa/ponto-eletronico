<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWasFolgaToPonto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ponto', function (Blueprint $table) {
            $table->boolean('was_folga')->default(false)->after('saida_noite');
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
            $table->dropColumn('was_folga');
        });
    }
}
