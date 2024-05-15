<?php

namespace App\Http\Controllers\PontoEletronico;


use Request;
use App\Ponto;
use App\Horario;
use App\Ausencia;

use Carbon\Carbon;
use App\Utilizador;
use App\Http\Requests;
use App\TiposAusencia;
use App\Services\GestaoDePontos;
use Illuminate\Support\Facades\DB;
use App\Services\GestaoDeAusencias;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;


class DashboardPainelController extends PontoEletronicoController
{
    protected $gestaoPontos;
    protected $gestaoAusencias;

    public function __construct()
    {
        $this->middleware('authPainelMiddleware');
    }

    public function index($ano_mes_atual)
    {
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        $utilizador = Utilizador::where('id', $utilizador_id)->first();

        $prox_mes = Carbon::createFromFormat('Y-m', $ano_mes_atual)->addMonths(1);
        $prev_mes = Carbon::createFromFormat('Y-m', $ano_mes_atual)->subMonths(1);

        $registos_ponto = GestaoDePontos::getRegistosPontoMes($prev_mes, $prox_mes);
        $registos_ausencia = GestaoDeAusencias::getRegistosAusenciaMes($prev_mes, $prox_mes);

        $numero_folgas_mes = GestaoDeAusencias::countAusenciasMes($registos_ausencia);
        $total_horas_trabalhadas = GestaoDePontos::countHorasTrabalhadas($registos_ponto);

        $turnos = Horario::where('regime_id', $utilizador->regime)->get();
        $today = Carbon::now();
        //Calcular o periodo atual.
            //Se o dia for maior ou igual a 16:
                // Inicio Periodo = 16 - Mes Atual
                //Fim Periodo = 15  - Mes Atual + 1
            //Se o dia for menor que 16:
                //Inicio Periodo = 16 - Mes atual - 1
                //Fim periodo = 15 - Mes atual
        //Se a data de submissão não pertencer ao intervalo do periodo atual, erro.
        if ($today->day >= 16) {
            $inicio_periodo = Carbon::createFromFormat('Y-m-d', $today->year . '-' . $today->month . '-16');
            $fim_periodo = $inicio_periodo->copy()->addMonth()->subDay();

        } else {
            $inicio_periodo = Carbon::createFromFormat('Y-m-d', $today->year . '-' . ($today->month - 1) . '-16');
            $fim_periodo =$inicio_periodo->copy()->addMonth()->subDay();
        }
        $is_atual = false;
        if(Carbon::createFromFormat('Y-m',$ano_mes_atual)->locale('pt')->monthName == $fim_periodo->locale('pt')->monthName){
            $is_atual = true;
        }
        return view('pontoeletronico/dashboard/dashboard-painel', [
            'utilizador' => $utilizador,
            'ano_mes_atual' => $ano_mes_atual,
            'prox_mes' => $prox_mes,
            'prev_mes' => $prev_mes,
            'registos_ponto' => $registos_ponto,
            'registos_ausencia' => $registos_ausencia,
            'horas_mes' => $total_horas_trabalhadas,
            'folgas_mes' => $numero_folgas_mes,
            'turnos' => $turnos,
            'is_atual' => $is_atual
            ]);
    }
}
