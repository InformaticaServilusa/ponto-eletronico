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
            ['descricao' => 'Folga', 'abreviatura' => 'F'],
            ['descricao' => 'Ferias', 'abreviatura' => 'FE'],
            ['descricao' => 'Falta', 'abreviatura' => 'FA'],
            ['descricao' => 'Licença', 'abreviatura' => 'L'],
            ['descricao' => 'Atestado', 'abreviatura' => 'AT'],
            ['descricao' => 'Outros', 'abreviatura' => 'OA'],
        ];

        foreach ($tipo_ausencia as $tipo_ausencia) {
            DB::table('tipo_ausencia')->insert($tipo_ausencia);
        }
    }
}
