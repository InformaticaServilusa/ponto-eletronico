<?php

namespace App\Services;

use App\Ausencia;
use Carbon\Carbon;
use App\Utilizador;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\AusenciaStoreRequest;

class GestaoDeAusencias
{
    public static function countAusenciasMes($registos_ausencia)
    {
        $faltas_mes = 0;
        foreach ($registos_ausencia as $ausencia) {
            $horas_ausencia = 0;
            if ($ausencia->hora_inicio == null && $ausencia->hora_fim == null) {
                $faltas_mes++;
            } else {
                $horas_ausencia += Carbon::parse($ausencia->hora_inicio)->diffInHours(Carbon::parse($ausencia->hora_fim));
            }

            $faltas_mes += $horas_ausencia / 8;
        }
        return $faltas_mes;
    }

    public function store(AusenciaStoreRequest $request)
    {
        $data = $request->validated();
    }

    public function editarAusencia(AusenciaStoreRequest $request)
    {
        $data = $request->validated();
        $ausencia = Ausencia::find($data['registo_id']);
        $ano_mes_atual = Carbon::now();
        if ((int)$ano_mes_atual->format('d') > 15) {
            $ano_mes_atual->addMonths(1);
        }
        if (!$ausencia) {
            $msg = "Registo não encontrado!";
            Session::put('status.msg', $msg);
            return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual]);
        }
        if ($request->input('obs_colab') == "") {
            $ausencia->obs_colab = "Correcção";
        }
        if ($data['hora_inicio'] == null && $data['hora_fim'] == null) {
            $ausencia->horas_ausencia = 8;
        } else {
            $ausencia->horas_ausencia = Carbon::parse($data['hora_inicio'])->diffInHours(Carbon::parse($data['hora_fim']));
        }

        $ausencia->fill($data);
        $ausencia->save();
        $msg = "Registo editado com sucesso!";
        Session::put('status.msg', $msg);
        return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual->format('Y-m')]);
    }

    public static function getRegistosAusenciaMes($mes_inicio, $mes_fim)
    {
        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));
        $registos_ausencia = Ausencia::where(
            'utilizador_id',
            $utilizador->id
        )
            ->where(
                'data',
                '>=',
                $mes_inicio->format('Y-m') . '-16'
            )
            ->where(
                'data',
                '<=',
                $mes_fim->format('Y-m') . '-15'
            )->get();
        $registos_ausencia = $registos_ausencia->keyBy('data');
        return $registos_ausencia;
    }
}
