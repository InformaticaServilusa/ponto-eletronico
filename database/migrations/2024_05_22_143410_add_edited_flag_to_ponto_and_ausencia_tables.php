<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditedFlagToPontoAndAusenciaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ponto', function (Blueprint $table) {
            $table->boolean('edited')->default(false);
        });
        Schema::table('ausencia', function (Blueprint $table) {
            $table->boolean('edited')->default(false);
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
            $table->dropColumn('edited');
        });
        Schema::table('ausencia', function (Blueprint $table) {
            $table->dropColumn('edited');
        });
    }
}
