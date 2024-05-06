<?php

use Illuminate\Database\Seeder;

class TipoAusenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipo_ausencia = [
            ['descricao' => 'Folga'],
            ['descricao' => 'Ferias'],
            ['descricao' => 'Falta'],
            ['descricao' => 'Licença'],
            ['descricao' => 'Atestado'],
            ['descricao' => 'Outros'],
        ];

        foreach ($tipo_ausencia as $tipo_ausencia) {
            DB::table('tipo_ausencia')->insert($tipo_ausencia);
        }
    }
}
