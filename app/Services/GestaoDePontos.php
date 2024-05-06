<?php

namespace App\Services;

use DB;
use App\Ponto;
use Carbon\Carbon;
use App\Utilizador;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\PontoStoreRequest;

class GestaoDePontos
{
    public static function countHorasTrabalhadas($pontos)
    {
        $horas_trabalhadas = 0;
        foreach ($pontos as $ponto) {
            $periodos = [
                ['entrada' => $ponto->entrada_manha, 'saida' => $ponto->saida_manha],
                ['entrada' => $ponto->entrada_tarde, 'saida' => $ponto->saida_tarde],
                ['entrada' => $ponto->entrada_noite, 'saida' => $ponto->saida_noite]
            ];

            foreach ($periodos as $periodo) {
                $entrada = Carbon::parse($periodo['entrada']) ?? null;
                $saida = Carbon::parse($periodo['saida']) ?? null;

                if ($entrada && $saida && ($entrada->isValid() && $saida->isValid())) {
                    $horas_trabalhadas += $saida->diffInMinutes($entrada);
                }
            }
        }

        $horas_trabalhadas = CarbonInterval::minutes($horas_trabalhadas)->cascade();
        return $horas_trabalhadas->totalHours;
    }

    public static function editarPonto(PontoStoreRequest $request)
    {
        $data = $request->validated();
        $ponto = Ponto::find($data['registo_id']);
        $ano_mes_atual = Carbon::now();
        if ((int)$ano_mes_atual->format('d') > 15) {
            $ano_mes_atual->addMonths(1);
        }
        if (!$ponto) {
            $msg = "Registo não encontrado!";
            Session::put('status.msg', $msg);
            return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual]);
        }
        if ($request->input('obs_colab') == "") {
            $ponto->obs_colab = "Correcção";
        }
        $ponto->fill($data);
        $total_horas = Carbon::parse($request->input('entrada_manha'))->diffInHours($request->input('saida_manha'))
            + Carbon::parse($request->input('entrada_tarde'))->diffInHours($request->input('saida_tarde'))
            + Carbon::parse($request->input('entrada_noite'))->diffInHours($request->input('saida_noite'));
        $ponto->total_horas_trabalhadas = $total_horas;
        if ($total_horas !== 8) {
            $ponto->status = 0;
        } else {
            $ponto->status = 1;
        }
        $ponto->save();
        $msg = "Registo editado com sucesso!";
        Session::put('status.msg', $msg);
        return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual->format('Y-m')]);
    }

    public static function getRegistosPontoMes($mes_inicio, $mes_fim)
    {
        //DB::enableQueryLog();

        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));
        $registos_ponto = Ponto::where('utilizador_id', $utilizador->id)
            ->where('data', '>=', $mes_inicio->format('Y-m') . '-16')
            ->where('data', '<=', $mes_fim->format('Y-m') . '-15')
            ->get();

        //$queries = \DB::getQueryLog();
        // Log or output the queries as needed
        // For example:
        // \Log::debug($queries);

        $registo_ponto = $registos_ponto->keyBy('data');
        return $registo_ponto;
    }
}
