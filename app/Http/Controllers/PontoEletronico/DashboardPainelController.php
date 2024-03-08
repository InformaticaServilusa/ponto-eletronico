<?php

namespace App\Http\Controllers\PontoEletronico;


use Request;
use App\Ponto;
use Carbon\Carbon;
use App\Utilizador;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;


class DashboardPainelController extends PontoEletronicoController
{

    public function __construct()
    {
        $this->middleware('authPainelMiddleware');
    }

    public function index($ano_atual, $mes_atual)
    {
        //TODO: CONTROLAR PASSAGENS DE ANO!
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        $utilizador = Utilizador::where('id', $utilizador_id)->first();

        $prox_mes = sprintf('%02d', intval($mes_atual) + 1);
        $registos_ponto = Ponto::where(
            'utilizador_id', $utilizador->id)
            ->where(
                'data', '>=', $ano_atual . '-' . $mes_atual . '-16'
            )
            ->where(
                'data', '<=', $ano_atual . '-' . $prox_mes . '-15'
            )->get();

        $registos_ponto = $registos_ponto->keyBy('data');
        $numero_folgas_mes = $registos_ponto->where('tipo_ponto_id', 2)->count();
        $total_horas_trabalhadas = 0;
        foreach($registos_ponto as $ponto){
            $am_in = Carbon::parse($ponto->entrada_manha);
            $am_out = Carbon::parse($ponto->saida_manha);
            $pm_in = Carbon::parse($ponto->entrada_tarde);
            $pm_out = Carbon::parse($ponto->saida_tarde);
            if($am_in->isValid() && $am_out->isValid() && ($am_in->diffInMinutes($am_out) > 0)){
                $total_horas_trabalhadas += $am_in->diffInHours($am_out);
            }
            if($pm_in->isValid() && $pm_out->isValid() && ($pm_in->diffInMinutes($pm_out) > 0)){
                $total_horas_trabalhadas += $pm_in->diffInHours($pm_out);
            }
        }

        return view('pontoeletronico/dashboard/dashboard-painel', [
            'utilizador' => $utilizador,
            'ano_atual' => $ano_atual,
            'mes_atual' => $mes_atual,
            'prox_mes' => $prox_mes,
            'registos_ponto' => $registos_ponto,
            'horas_mes' => $total_horas_trabalhadas,
            'folgas_mes' => $numero_folgas_mes,
        ]);
    }

    private static function getCurrentMonth()
    {
        $mes[1] = 'Janeiro';
        $mes[2] = 'Fevereiro';
        $mes[3] = 'Março';
        $mes[4] = 'Abril';
        $mes[5] = 'Maio';
        $mes[6] = 'Junho';
        $mes[7] = 'Julho';
        $mes[8] = 'Agosto';
        $mes[9] = 'Setembro';
        $mes[10] = 'Outubro';
        $mes[11] = 'Novembro';
        $mes[12] = 'Dezembro';

        $mes_extenso = $mes[Date('n')];
        return $mes_extenso;
    }
}
