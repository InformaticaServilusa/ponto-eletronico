<?php

use Illuminate\Database\Seeder;

class HorarioRegimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //TODO: DEPOIS ARRANJAR OS HORARIOS CONFORME
        //TODO: ARANJAR OS HORARIOS DO CALLCENTER PARA QUE TENHA 3 TURNOS
        $horarios = [
            [
                'descricao' => 'Horário de trabalho para regime administrativo',
                'entrada_manha' => '09:00',
                'saida_manha' => '13:00',
                'entrada_tarde' => '14:00',
                'saida_tarde' => '18:00',
                'regime_id' => 1,
                'horas_semanais' => 40,
                'horas_diarias' => 8,
                'dias_max_seguidos' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descricao' => 'Horário de trabalho para regime de Pool/Comerciais',
                'entrada_manha' => '09:00',
                'saida_manha' => '13:00',
                'entrada_tarde' => '14:00',
                'saida_tarde' => '18:00',
                'regime_id' => 2,
                'horas_semanais' => '40',
                'horas_diarias' => '8',
                'dias_max_seguidos' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descricao' => 'Horário de trabalho para regime de call center',
                'entrada_manha' => '09:00',
                'saida_manha' => '13:00',
                'entrada_tarde' => '14:00',
                'saida_tarde' => '18:00',
                'regime_id' => 3,
                'horas_semanais' => '40',
                'horas_diarias' => '8',
                'dias_max_seguidos' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descricao' => 'Horário de trabalho para regime operacional',
                'entrada_manha' => '09:00',
                'saida_manha' => '13:00',
                'entrada_tarde' => '14:00',
                'saida_tarde' => '18:00',
                'regime_id' => 4,
                'horas_semanais' => '40',
                'horas_diarias' => '8',
                'dias_max_seguidos' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach($horarios as $horario){
            DB::table('horario')->insert($horario);
        }
    }
}
