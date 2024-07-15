<?php

namespace App\Http\Controllers\PontoEletronico;


use Request;
use App\Ponto;
use App\Horario;
use App\Ausencia;
use App\ControloRHUtilizadorMes;

use Carbon\Carbon;
use App\Utilizador;
use App\Http\Requests;
use App\TiposAusencia;
use Carbon\CarbonPeriod;
use App\Services\MesController;
use App\Services\GestaoDePontos;
use Illuminate\Support\Facades\DB;
use App\Services\GestaoDeAusencias;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;



class DashboardPainelController extends PontoEletronicoController
{
    protected $gestaoPontos;
    protected $gestaoAusencias;

    public function __construct()
    {
        // $this->middleware('authPainelMiddleware');
    }

    public function index($ano_mes = null)
    {
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        $utilizador = Utilizador::where('id', $utilizador_id)->first();
        //Entro com o periodo de processamento: 'Janeiro (16 Dez - 15 Jan), Fevereiro(16 Jan - 15 Fev), Marco(16 Fev, 15 Mar) ...)
        if ($ano_mes == null) {
            $data_atual = Carbon::now();
            if ($data_atual->day >= 16) {
                $data_inicio = $data_atual->copy()->format('Y-m') . '-16';
                $data_fim = $data_atual->copy()->addMonthNoOverflow()->format('Y-m') . '-15';
                $ano_mes = $data_atual->copy()->addMonthNoOverflow()->format('Y-m');
            } else {
                $data_inicio = $data_atual->copy()->subMonthNoOverflow()->format('Y-m') . '-16';
                $data_fim = $data_atual->copy()->format('Y-m') . '-15';
                $ano_mes = $data_atual->copy()->format('Y-m');
            }
        } else {
            $data_inicio = Carbon::createFromFormat('Y-m-d', $ano_mes . '-16')->subMonthNoOverflow()->format('Y-m-d');
            $data_fim = Carbon::createFromFormat('Y-m-d', $ano_mes . '-15')->format('Y-m-d');
        }

        //preciso de verificar os fins de semana do mês, e caso não estejam marcados como ausencia, marca-los.
        //verifica se existe registo em controlo_user_mes e caso exista, se tem a flag weekends_marked a false
        //se tive nao existir, cria o registo, se existir, executa o codigo abaixo e marca a flag como true
        $controlo_user_mes = ControloRHUtilizadorMes::where('utilizador_id', $utilizador->id)->where('ano_mes', $ano_mes)->first();
        $periodo = CarbonPeriod::create($data_inicio, $data_fim);
        if (!$controlo_user_mes) {
            try {
                $controlo_user_mes = new ControloRHUtilizadorMes();
                $controlo_user_mes->utilizador_id = $utilizador->id;
                $controlo_user_mes->ano_mes = $ano_mes;
                $controlo_user_mes->save();
            } catch (\Exception $e) {
                Log::info("message: " . $e->getMessage());
                $msg = "Erro ao criar registo de controlo de utilizador";
                return redirect()->back()->with('error', $msg);
            }
        }

        if ($controlo_user_mes->weekends_marked == false) {
            foreach ($periodo as $date) {
                if ($date->isWeekend()) {
                    $ausencia = Ausencia::where('utilizador_id', $utilizador->id)->where('data', $date->format('Y-m-d'))->first();
                    $ponto = Ponto::where('utilizador_id', $utilizador->id)->where('data', $date->format('Y-m-d'))->first();
                    if (!$ausencia && !$ponto) {
                        try {
                            $ausencia = new Ausencia();
                            $ausencia->utilizador_id = $utilizador->id;
                            $ausencia->data = $date->format('Y-m-d');
                            $ausencia->tipo_ausencia_id = 1;
                            $ausencia->horas_ausencia = 8;
                            $ausencia->status = 1;
                            $ausencia->obs_colab = "Introduzida automaticamente pelo sistema. Fim de semana.";
                            $ausencia->hora_inicio = "09:00";
                            $ausencia->hora_fim = "18:00";
                            $ausencia->controlo_user_mes_id = $controlo_user_mes->id;
                            $ausencia->save();
                            $controlo_user_mes->horas_folga += 8;
                        } catch (\Exception $e) {
                            Log::info("message: " . $e->getMessage());
                            $msg = "Erro ao criar registo de ausencia";
                            return redirect()->back()->with('error', $msg);
                        }
                    }
                }
                $controlo_user_mes->weekends_marked = true;
                $controlo_user_mes->save();
            }
        }

        $registos_ponto = GestaoDePontos::getRegistosPontoMes($data_inicio, $data_fim);
        $registos_ausencia = GestaoDeAusencias::getRegistosAusenciaMes($data_inicio, $data_fim);

        $total_horas_trabalhadas = GestaoDePontos::count_horas_trabalhadas($registos_ponto);
        $ferias_mes = GestaoDeAusencias::get_ferias_mes($registos_ausencia);
        $numero_ausencias_mes = GestaoDeAusencias::count_ausencias_mes($registos_ausencia) - $ferias_mes;
        $numero_folgas_mes = GestaoDeAusencias::countFolgasMes($data_inicio, $data_fim);


        $turnos = Horario::where('regime_id', $utilizador->regime)->get();
        $today = Carbon::now();
        $is_atual = true;
        //today esta fora do intervalo, data_inicio - data_fim
        if ($today->format('Y-m-d') < $data_inicio || $today->format('Y-m-d') > $data_fim) {
            $is_atual = false;
        }
        $feriados_mes = MesController::get_feriados_mes($data_inicio, $data_fim);

        return view('pontoeletronico/dashboard/dashboard-painel', [
            'utilizador' => $utilizador,
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'registos_ponto' => $registos_ponto,
            'registos_ausencia' => $registos_ausencia,
            'horas_mes' => $total_horas_trabalhadas,
            'ausencias_mes' => $numero_ausencias_mes,
            'folgas_mes' => $numero_folgas_mes,
            'turnos' => $turnos,
            'is_atual' => $is_atual,
            'tipoAusencias' => TiposAusencia::all(),
            'feriados_mes' => $feriados_mes,
            'ferias_mes' => $ferias_mes,
            'periodo' => $periodo,
            'controlo_user_mes' => $controlo_user_mes,
            'ano_mes' => $ano_mes,
        ]);
    }
}
