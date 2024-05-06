<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RegimeSeeder::class);
        $this->call(HorarioRegimeSeeder::class);
        $this->call(TipoPontoSeeder::class);
        $this->call(TipoAusenciaSeeder::class);
    }
}
