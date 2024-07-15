<?php

namespace App\Http\Controllers\PontoEletronico;


use Log;
use DateTime;
use App\Ponto;

use App\Horario;
use App\Usuario;
use App\Ausencia;
use Carbon\Carbon;
use App\Utilizador;
use App\PontoAjuste;
use Illuminate\Http\Request;
use App\ControloRHUtilizadorMes;
use App\Services\GestaoDePontos;
use App\Services\GestaoDeAusencias;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\PontoStoreRequest;
use App\Http\Requests\AusenciaStoreRequest;
use App\ControloRHUtilizadorMes as AppControloRHUtilizadorMes;


class PontoPainelController extends PontoEletronicoController
{
    public function __construct()
    {
        // $this->middleware('authPainelMiddleware');
    }

    public function submeterTrabalho(Request $request)
    {
        $data = json_decode($request->input('data'));
        $turno_id = $request->input('turno_id');
        $msg = "";
        if (is_array($data)) {
            foreach ($data as $processData) {
                $msg .=  $this->processDataTrabalho($processData, $turno_id);
            }
        } else {
            $msg =  $this->processDataTrabalho($data, $turno_id);
        }

        return $this->redirectDashboard($msg);
    }

    private function processDataTrabalho($data, $turno_id)
    {
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');

        $utilizador = Utilizador::where('id', $utilizador_id)->first();
        $data = Carbon::createFromFormat('Y-m-d', $data);
        $ponto = Ponto::where('utilizador_id', $utilizador->id)->where('data', $data->toDateString())->first();
        $ausencia = Ausencia::where('utilizador_id', $utilizador->id)->where('data', $data->toDateString())->first();
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
            $fim_periodo = $inicio_periodo->copy()->addMonth()->subDay();
        }
        $periodo_atual = [
            "inicio" => $inicio_periodo->format('Y-m-d'),
            "fim" => $fim_periodo->format('Y-m-d')
        ];
        $data = $data->format('Y-m-d');
        if (isset($turno_id) && $turno_id != null) {
            $horario = Horario::where('id', $turno_id)->first();
        } else {
            $horario = Horario::where('regime_id', $utilizador->regime)->first();
        }
        $ponto_control_rh = ControloRHUtilizadorMes::where('utilizador_id', $utilizador->id)->where('ano_mes', substr($periodo_atual['fim'], 0, 7))->first();

        //TODO: Melhorar para que as mensagens de submissao/erro tenham um cabeçalho e uma lista de datas em vez de varios cabeçalhos e varias datas
        if ($ponto || $ausencia) {
            $msg = "Já existe um ponto para o dia " . $data . "\\n";
            return $msg;
        } elseif ($data > $periodo_atual['fim'] || $data < $periodo_atual['inicio']) {
            $msg = "Data inválida!\\n Fora do periodo atual.\\n";
            return $msg;
        } else {
            $ponto = new Ponto();
            $ponto->utilizador_id = $utilizador->id;
            $ponto->data = $data;
            $ponto->total_horas_trabalhadas = $horario->horas_diarias;
            $ponto->fill($horario->toArray());
            $ponto->status = 1;
            $ponto->tipo_ponto_id = 1;
            $ponto->controlo_user_mes_id = $ponto_control_rh->id;
            $ponto->save();
            if ($ponto_control_rh) {
                $ponto_control_rh->horas_trabalhadas += $horario->horas_diarias;
            } else {
                $ponto_control_rh = new ControloRHUtilizadorMes();
                $ponto_control_rh->utilizador_id = $utilizador->id;
                $ponto_control_rh->ano_mes = substr($periodo_atual['fim'], 0, 7);
                $ponto_control_rh->horas_trabalhadas = $horario->horas_diarias;
            }
            $ponto_control_rh->save();
            $msg = "Ponto submetido com sucesso para o dia " . $data . "\\n";
            return $msg;
        }
    }

    public function submeterAusencia(Request $request)
    {
        //Vou ter de receber um tipo de ausencia
        $data = json_decode($request->input('data'));
        $tipo_ausencia = $request->input('tipo_ausencia_select');
        $utilizador_id = $this->checkSession();

        if (is_array($data)) {
            foreach ($data as $processData) {
                return $this->processDataFolga($processData, $tipo_ausencia);
            }
        } else {
            return $this->processDataFolga($data, $tipo_ausencia);
        }
    }

    private function processDataFolga($data, $tipo_ausencia)
    {
        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'))->first();
        $data = Carbon::createFromFormat('Y-m-d', $data);
        $ponto = Ponto::where('utilizador_id', $utilizador->id)->where('data', $data->toDateString())->first();
        $ausencia = Ausencia::where('utilizador_id', $utilizador->id)->where('data', $data->toDateString())->first();
        if ($data->day >= 16) {
            $data_inicio = $data->copy()->format('Y-m') . '-16';
            $data_fim = $data->copy()->addMonthNoOverflow()->format('Y-m') . '-15';
            $ano_mes = $data->copy()->addMonthNoOverflow()->format('Y-m');
        } else {
            $data_inicio = $data->copy()->subMonthNoOverflow()->format('Y-m') . '-16';
            $data_fim = $data->copy()->format('Y-m') . '-15';
            $ano_mes = $data->copy()->format('Y-m');
        }
        $controlo_user_mes = ControloRHUtilizadorMes::where('utilizador_id', $utilizador->id)->where('ano_mes', $ano_mes)->first();

        if ($ponto || $ausencia) {
            $msg = "Já existe um" . ($ponto !== null ? " Ponto" : "a Ausencia") . " para este dia!";
            return $this->redirectDashboard($msg);
        } else {
            try {
                $ausencia = new Ausencia();
                $ausencia->utilizador_id = $utilizador->id;
                $ausencia->data = $data;
                $ausencia->tipo_ausencia_id = $tipo_ausencia;
                $ausencia->status = 1;
                $ausencia->save();
                $msg = "Ausência submetida com sucesso!";
                return $this->redirectDashboard($msg);
            } catch (\Exception $e) {
                Log::error('Erro ao submeter a ausência: ' . $e->getMessage());
                $msg = "Erro ao submeter a ausência!";
                return $this->redirectDashboard($msg);
            }
        }
    }

    public function editar(PontoStoreRequest $request)
    {
        $tipo_registo = $request->input('registo_tipo');
        $gestaoPontos = new GestaoDePontos();
        return $gestaoPontos->editarPonto($request);
    }

    public function eliminar($tipo, $registo_id)
    {
        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));
        try {
            if ($tipo === 'ponto') {
                $ponto = Ponto::find($registo_id);
                $controlo_user_mes = $ponto->controlo_user_mes;
                if (!$ponto) {
                    $msg = "Registo não encontrado!";
                    return $this->redirectDashboard($msg);
                }
                $controlo_user_mes->horas_trabalhadas -= $ponto->total_horas_trabalhadas;
                if ($ponto->total_horas_trabalhadas < 8) {
                    $controlo_user_mes->horas_ausencia -= (8 - $ponto->total_horas_trabalhadas);
                }
                if ($ponto->is_folga()) {
                    $controlo_user_mes->folgas_trabalhadas -= 1;
                }
                try {
                    $ponto->delete();
                    $controlo_user_mes->save();
                    $msg = "Registo eliminado com sucesso!";
                    Log::info("Record deleted successfully: " . $registo_id . "by " . $utilizador->id);
                    return $this->redirectDashboard($msg);
                } catch (\Exception $e) {
                    Log::error('Erro ao eliminar o registo: ' . $e->getMessage());
                    $msg = "Erro ao eliminar o registo!";
                    return $this->redirectDashboard($msg);
                }
            } elseif ($tipo === 'ausencia') {
                $ausencia = Ausencia::find($registo_id);
                $controlo_user_mes = $ausencia->controlo_user_mes;
                if (!$ausencia) {
                    $msg = "Registo não encontrado!";
                    return $this->redirectDashboard($msg);
                }
                if ($ausencia->is_ferias()) {
                    $controlo_user_mes->ferias -= 1;
                } elseif ($ausencia->is_folga()) {
                    $controlo_user_mes->horas_folga -= $ausencia->horas_ausencia;
                    if ($ausencia->horas_ausencia < 8) {
                        $controlo_user_mes->horas_trabalhadas -= (8 - $ausencia->horas_ausencia);
                    }
                } else {
                    $controlo_user_mes->horas_ausencia -= $ausencia->horas_ausencia;
                    if ($ausencia->horas_ausencia < 8) {
                        $controlo_user_mes->horas_trabalhadas -= (8 - $ausencia->horas_ausencia);
                    }
                }

                try {
                    $ausencia->delete();
                    $controlo_user_mes->save();
                    Log::info("Record deleted successfully: " . $registo_id . "by " . $utilizador->id);
                    $msg = "Registo eliminado com sucesso!";
                    return $this->redirectDashboard($msg);
                } catch (\Exception $e) {
                    Log::error('Erro ao eliminar o registo: ' . $e->getMessage());
                    $msg = "Erro ao eliminar o registo!";
                    return $this->redirectDashboard($msg);
                }
            } else {
                $msg = "Tipo de registo inválido!";
                return $this->redirectDashboard($msg);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao eliminar o registo: ' . $e->getMessage());
            $msg = "Erro ao eliminar o registo!";
            return $this->redirectDashboard($msg);
        }
    }

    private function redirectDashboard($msg)
    {
        Session::put('status.msg', $msg);
        $ano_mes_dia = Carbon::now();

        if ((int)$ano_mes_dia->format('d') > 15) {
            $ano_mes_dia->addMonths(1);
        }

        return redirect(getenv('APP_URL') . '/painel/dashboard/' . $ano_mes_dia->format('Y-m'));
    }
    private function checkSession()
    {
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        if ($utilizador_id == null) {
            return redirect(getenv('APP_URL') . '/');
        }
        return $utilizador_id;
    }
}
