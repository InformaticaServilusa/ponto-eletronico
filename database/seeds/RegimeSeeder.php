<?php

use Illuminate\Database\Seeder;

class RegimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regimes = [
            ['id' => 1, 'descricao' => 'Administrativo', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'descricao' => 'Comercial', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'descricao' => 'CallCenter', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'descricao' => 'Operacional', 'created_at' => now(), 'updated_at' => now()],
        ];
        foreach($regimes as $regime){
            DB::table('regime')->insert($regime);
        }
    }
}
