<?php

namespace App\Services;

use App\Ausencia;
use App\Http\Controllers\ControloRHUtilizadorMes;
use Carbon\Carbon;
use App\Utilizador;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\AusenciaStoreRequest;

class GestaoDeAusencias
{
    public static function count_ausencias_mes($registos_ausencia)
    {
        $faltas_mes = 0;
        foreach ($registos_ausencia as $ausencia) {
            $horas_ausencia = 0;
            if ($ausencia->tipo_ausencia_id != 1) {
                if ($ausencia->hora_inicio == null && $ausencia->hora_fim == null) {
                    $faltas_mes++;
                } else {
                    $horas_ausencia += Carbon::parse($ausencia->hora_inicio)->diffInHours(Carbon::parse($ausencia->hora_fim));
                }

                $faltas_mes += $horas_ausencia / 8;
            }
        }
        return $faltas_mes;
    }
    public static function countFolgasMes($data_inicio, $data_fim)
    {
        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));
        $registos_ausencia = Ausencia::where(
            'utilizador_id',
            $utilizador->id
        )->where(
            'data',
            '>=',
            $data_inicio
        )->where(
            'data',
            '<=',
            $data_fim
        )->get();
        $registos_ausencia = $registos_ausencia->keyBy('data');
        $folgas_mes = 0;
        foreach ($registos_ausencia as $ausencia) {
            if ($ausencia->tipo_ausencia_id == 1) {
                $folgas_mes++;
            }
        }
        return $folgas_mes;
    }

    public function store(AusenciaStoreRequest $request)
    {
        $data = $request->validated();
    }

    public function editarAusencia(AusenciaStoreRequest $request)
    {
        $logged_user = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));
        $data = $request->validated();
        $ausencia = Ausencia::find($data['registo_id']);
        $ano_mes_atual = Carbon::create($ausencia->controlo_user_mes->ano_mes);
        $total_anterior = $ausencia->horas_ausencia;
        $controlo_user_mes = $ausencia->controlo_user_mes;

        if (!$ausencia) {
            $msg = "Registo não encontrado!";
            Session::put('status.msg', $msg);
            return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual->format('Y-m')]);
        }
        if (empty($data['obs_colab'])) {
            unset($data['obs_colab']);
            $ausencia->obs_colab = "Correcção";
        }
        if ($data['hora_inicio'] == null && $data['hora_fim'] == null) {
            $ausencia->horas_ausencia = 8;
            $ausencia->status = 1;
        } else {
            $total_ausencia = Carbon::parse($data['hora_inicio'])->diffInHours(Carbon::parse($data['hora_fim']));
            if ($total_ausencia != 8) {
                $ausencia->status = 0;
            }
            if (Carbon::parse($data['hora_fim'])->hour > 13) {
                $total_ausencia -= 1;
            }
            $ausencia->horas_ausencia = $total_ausencia;
            $controlo_user_mes->horas_ausencia = max(0, $controlo_user_mes->horas_ausencia - ($total_anterior - $total_ausencia));
            $controlo_user_mes->horas_trabalhadas = max(0, $controlo_user_mes->horas_trabalhadas - ($total_ausencia - $total_anterior));
        }

        try {
            if (!empty($data['anexo'])) {
                $data['anexo'] = $data['anexo']->store('docsJustifAusencias/' . $logged_user->id, 'public');
            }
            $ausencia->fill($data);
            $ausencia->edited = true;
            $ausencia->save();
            $controlo_user_mes->save();
            $msg = "Registo editado com sucesso!";
            Session::put('status.msg', $msg);
            return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual->format('Y-m')]);
        } catch (\Exception $e) {
            $msg = "Erro ao editar registo!";
            Log::error($e->getMessage());
            Session::put('status.msg', $msg);
            return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual->format('Y-m')]);
        }
    }

    public static function getRegistosAusenciaMes($data_inicio, $data_fim)
    {
        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));
        $registos_ausencia = Ausencia::where(
            'utilizador_id',
            $utilizador->id
        )->where(
            'data',
            '>=',
            $data_inicio
        )->where(
            'data',
            '<=',
            $data_fim
        )->get();
        $registos_ausencia = $registos_ausencia->keyBy('data');
        return $registos_ausencia;
    }

    public static function getFolgasMes($data_inicio, $data_fim)
    {
        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));
        $registos_ausencia = Ausencia::where(
            'utilizador_id',
            $utilizador->id
        )
            ->where(
                'data',
                '>=',
                $data_inicio
            )
            ->where(
                'data',
                '<=',
                $data_fim
            )
            ->where(
                'tipo_ausencia_id',
                1
            )->get();
        $registos_ausencia = $registos_ausencia->keyBy('data');
        return $registos_ausencia;
    }
    public static function get_regausen_colab_mes($data_inicio, $data_fim, $colab)
    {
        $registos_ausencia = Ausencia::where(
            'utilizador_id',
            $colab->id
        )
            ->where(
                'data',
                '>=',
                $data_inicio
            )
            ->where(
                'data',
                '<=',
                $data_fim
            )->get();
        $registos_ausencia = $registos_ausencia->keyBy('data');
        return $registos_ausencia;
    }

    public static function get_ferias_mes($registos_ausencia)
    {
        if (!empty($registos_ausencia)) {
            $ferias_mes = $registos_ausencia->filter(function ($ausencia) {
                return $ausencia->tipo_ausencia_id == 2;
            })->count();
            return $ferias_mes;
        }
        return 0;
    }
}
