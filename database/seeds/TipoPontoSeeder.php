<?php

use Illuminate\Database\Seeder;

class TipoPontoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipo_ponto = [
            ['descricao' => 'Dia Trabalho'],
            ['descricao' => 'Folga'],
            ['descricao' => 'Ferias'],
            ['descricao' => 'Falta'],
            ['descricao' => 'Feriado'],
            ['descricao' => 'Licença'],
            ['descricao' => 'Atestado'],
            ['descricao' => 'Outros'],
        ];

        foreach ($tipo_ponto as $tipo_ponto) {
            DB::table('tipo_ponto')->insert($tipo_ponto);
        }
    }
}
