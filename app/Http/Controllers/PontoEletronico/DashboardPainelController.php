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
            ]);
    }
}
