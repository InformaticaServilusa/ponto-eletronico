<?php

namespace App\Services;

use App\ControloRHUtilizadorMes;
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
    public static function count_horas_trabalhadas($pontos)
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
        //TODO: MESMO QUE TENHA SIDO UM ENGANO, CONTA COMO EDITADA
        $data = $request->validated();
        if (isset($data['was_folga'])) {
            $data['tipo_ponto_id'] = 2;
        } else {
            $data['tipo_ponto_id'] = 1;
        }
        $ponto = Ponto::find($data['registo_id']);
        $ano_mes_atual = Carbon::now();
        if ((int)$ano_mes_atual->format('d') > 15) {
            $ano_mes_atual->addMonths(1);
        }
        $controlo_user_mes = ControloRHUtilizadorMes::where('utilizador_id', $ponto->utilizador_id)
            ->where('ano_mes', $ano_mes_atual->format('Y-m'))->first();
        if (!$ponto) {
            $msg = "Registo não encontrado!";
            Session::put('status.msg', $msg);
            return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual]);
        }
        if ($request->input('obs_colab') == "") {
            $ponto->obs_colab = "Correcção";
        }
        if (isset($data['was_folga']) && $ponto->is_folga() == 0) {
            $controlo_user_mes->folgas_trabalhadas += 1;
        } elseif (!isset($data['was_folga']) && $ponto->is_folga() == 1) {
            $controlo_user_mes->folgas_trabalhadas -= 1;
        }
        unset($data['was_folga']);
        $total_anterior = $ponto->total_horas_trabalhadas;
        $ponto->fill($data);
        $total_horas = Carbon::parse($request->input('entrada_manha'))->diffInHours($request->input('saida_manha'))
            + Carbon::parse($request->input('entrada_tarde'))->diffInHours($request->input('saida_tarde'))
            + Carbon::parse($request->input('entrada_noite'))->diffInHours($request->input('saida_noite'));
        $controlo_user_mes->horas_trabalhadas = $controlo_user_mes->horas_trabalhadas - ($total_anterior - $total_horas);
        $ponto->total_horas_trabalhadas = $total_horas;

        if ($total_horas !== 8 ) {
            $ponto->status = 0;
        } else {
            $ponto->status = 1;
        }

        if ($total_horas < 8) {
            $controlo_user_mes->horas_ausencia += (8 - $total_horas);
        } else {
            $controlo_user_mes->horas_ausencia -= ($total_horas - $total_anterior);
        }

        $ponto->edited = true;
        $controlo_user_mes->save();
        $ponto->save();
        $msg = "Registo editado com sucesso!";
        Session::put('status.msg', $msg);
        return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual->format('Y-m')]);
    }

    public static function getRegistosPontoMes($data_inicio, $data_fim)
    {
        //DB::enableQueryLog();
        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));
        $registos_ponto = Ponto::where('utilizador_id', $utilizador->id)
            ->where('data', '>=', $data_inicio)
            ->where('data', '<=', $data_fim)
            ->get();

        //$queries = \DB::getQueryLog();
        // Log or output the queries as needed
        // For example:
        // \Log::debug($queries);

        $registo_ponto = $registos_ponto->keyBy('data');
        return $registo_ponto;
    }
    public static function get_regponto_colab_mes($data_inicio, $data_fim, $colab)
    {
        //DB::enableQueryLog();

        $registos_ponto = Ponto::where('utilizador_id', $colab->id)
            ->where('data', '>=', $data_inicio)
            ->where('data', '<=', $data_fim)
            ->get();

        //$queries = \DB::getQueryLog();
        // Log or output the queries as needed
        // For example:
        // \Log::debug($queries);

        $registo_ponto = $registos_ponto->keyBy('data');
        return $registo_ponto;
    }
}
