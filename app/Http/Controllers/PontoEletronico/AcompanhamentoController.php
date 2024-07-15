<?php

namespace App\Http\Controllers\PontoEletronico;


use Request;
use App\Ponto;
use App\Usuario;

use App\Ausencia;
use Carbon\Carbon;
use App\Utilizador;
use App\Http\Requests;
use App\Services\MesController;
use App\Services\GestaoDePontos;
use Illuminate\Support\Facades\DB;
use App\Services\GestaoDeAusencias;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class AcompanhamentoController extends PontoEletronicoController
{

    public function __construct()
    {
        //$this->middleware('CoordenadorMiddleware',  ['except' => ['dashboardRH']]);
        //$this->middleware('DepRHMiddleware',  ['only' => ['dashboardRH']]);
    }
    //Quando entro dentro do index da Coordenação, o ano_mes já vem tratado de forma a que caso seja >= 16 seja do mes seguinte
    //para enquadrar com os meses de processamento salarial.
    public function index($ano_mes = null)
    {
        //Colaboradores sobre coordenação do utilizador logado
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        $utilizador = Utilizador::find($utilizador_id);
        $departamento = $utilizador->departamento;
        $all_coordenandos = Utilizador::where('coordenador_id', $utilizador_id)->orWhere('id', $utilizador->id)->get();;
        if (isset($_POST) && !empty($_POST['utilizador']) && $_POST['utilizador'] != 'ALL') {
            $coordenandos = Utilizador::where('id', $_POST['utilizador'])->get();
        } else {
            $coordenandos = Utilizador::where('coordenador_id', $utilizador_id)->orWhere('id', $utilizador->id)->get();
        }

        //Trata o periodo de processamento salarial
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

        // $ano_mes = Carbon::createFromFormat('Y-m', $ano_mes);
        // $inicio_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes->format('Y-m') . '-16')->subMonth();
        // $fim_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes->format('Y-m') . '-15');
        //Calculo das horas expectaveis de trabalho e ausencias do mes
        $feriados_periodo = MesController::get_feriados_mes($data_inicio, $data_fim);
        $numero_feriados = count($feriados_periodo);
        $feriados_fimdesemana = 0;
        //Ver quantos feriados ao fim de semana
        if ($feriados_periodo) {
            $feriados_fimdesemana = $feriados_periodo->keys()->filter(function ($key) {
                $dia = Carbon::createFromFormat('Y-m-d', $key);
                return $dia->isWeekend();
            })->count();
        }

        //Ha feriados que calham ao fim de semana, logo não subtraem as ausencias expectaveis nem as horas expectaveis
        $ausencias_expectaveis_mes = MesController::get_weekend_mes($data_inicio, $data_fim) + ($numero_feriados - $feriados_fimdesemana);
        $horas_expectaveis_trabalhadas_mes = MesController::get_horas_expect_mes($data_inicio, $data_fim) - ($numero_feriados - $feriados_fimdesemana) * 8;

        //get all coordenandos Ausencia and Ponto beetween $ano_mes-15 and ($ano_mes+1month)-16
        foreach ($coordenandos as $coordenando) {
            $ausencias = GestaoDeAusencias::get_regausen_colab_mes($data_inicio, $data_fim, $coordenando);
            $pontos = GestaoDePontos::get_regponto_colab_mes($data_inicio, $data_fim, $coordenando);

            //Para cada um dos Coordenandos
            //Contar o numero de horas de Ponto
            //Contar o numero de Ausencias
            $horas_trabalhadas = GestaoDePontos::count_horas_trabalhadas($pontos);;
            $coordenando->setHorasMes($horas_trabalhadas);

            $numero_ausencias = GestaoDeAusencias::count_ausencias_mes($ausencias);
            $numero_ferias = GestaoDeAusencias::get_ferias_mes($ausencias);
            $coordenando->setFaltasMes($numero_ausencias - $numero_ferias);
            $coordenando->setFeriasMes($numero_ferias);
            $coordenando->controlo_user_mes = $coordenando->controlo_user_mes()->where('ano_mes', $ano_mes)->first();
        }
        return view(
            'pontoeletronico/coordenacao/index-painel',
            compact([
                'coordenandos',
                'horas_expectaveis_trabalhadas_mes',
                'ausencias_expectaveis_mes',
                'numero_feriados',
                'ano_mes',
                'departamento',
                'data_inicio',
                'data_fim',
                'all_coordenandos',
                'utilizador'
            ])
        );
    }

    public function dashboardCoordenacao($ano_mes, $colaborador_id)
    {
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        $utilizador = Utilizador::where('id', $utilizador_id)->first();
        //Trata o periodo de pagamento
        $ano_mes = Carbon::createFromFormat('Y-m', $ano_mes);
        $inicio_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes->format('Y-m') . '-16')->subMonth();
        $fim_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes->format('Y-m') . '-15');

        // Vai buscar as faltas e presenças do colaborado para o mês.
        $pontos = Ponto::where('utilizador_id', $colaborador_id)
            ->where('data', '>=', $inicio_periodo->format('Y-m-d'))
            ->where('data', '<=', $fim_periodo->format('Y-m-d'))
            ->get();
        $ausencias = Ausencia::where('utilizador_id', $colaborador_id)
            ->where('data', '>=', $inicio_periodo->format('Y-m-d'))
            ->where('data', '<=', $fim_periodo->format('Y-m-d'))
            ->get();
        $pontos = $pontos->keyBy('data');
        $ausencias = $ausencias->keyBy('data');
        $colaborador = Utilizador::find($colaborador_id);
        $horas_trabalhadas = GestaoDePontos::count_horas_trabalhadas($pontos);
        $numero_ausencias = GestaoDeAusencias::count_ausencias_mes($ausencias);
        $controlo_user_mes = $colaborador->controlo_user_mes()->where('ano_mes', $ano_mes->format('Y-m'))->first();

        return view('pontoeletronico/coordenacao/dashboard-colaborador', compact([
            'colaborador',
            'horas_trabalhadas',
            'numero_ausencias',
            'ano_mes',
            'pontos',
            'ausencias',
            'inicio_periodo',
            'fim_periodo',
            'utilizador',
            'controlo_user_mes'
        ]));
    }

    public function dashboardRH($ano_mes = null)
    {
        // dd($ano_mes);
        $utilizador = session('utilizador');
        $all_utilizadores = Utilizador::all();
        $all_departamentos = Utilizador::pluck('departamento')->unique();

        if (isset($_POST) && (!empty($_POST['departamento']) || !empty($_POST['colaborador']))) {
            if (!empty($_POST['departamento'])) {
                $display_colaboradores = Utilizador::where('departamento', $_POST['departamento'])->get();
            } elseif (!empty($_POST['colaborador'])) {
                if ($_POST['colaborador'] != 'ALL') {
                    $display_colaboradores = Utilizador::where('id', $_POST['colaborador'])->get();
                } else {
                    $display_colaboradores = Utilizador::all();
                }
            }
        }
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
        $data = compact([
            'utilizador',
            'all_utilizadores',
            'all_departamentos',
            'data_inicio',
            'data_fim',
            'ano_mes',
        ]);

        if (isset($display_colaboradores)) {
            $data['display_colaboradores'] = $display_colaboradores;
            foreach ($display_colaboradores as $colaborador) {
                $colaborador->controlo_user_mes = $colaborador->controlo_user_mes()->where('ano_mes', $ano_mes)->first();
                $pontos = GestaoDePontos::get_regponto_colab_mes($data_inicio, $data_fim, $colaborador)->all();
                $ausencias = GestaoDeAusencias::get_regausen_colab_mes($data_inicio, $data_fim, $colaborador)->all();
                $final = array_merge($pontos, $ausencias);
                ksort($final);
                $colaborador->registos = $final;
            }
        }

        return view('pontoeletronico/coordenacao/dashboard-rh', $data);
    }

    public function index_download($usuario, $inicio, $fim)
    {

        // $usuario_admin = Session::get('login.ponto.painel.admin');
        // $usuario_id = Session::get('login.ponto.painel.usuario_id');

        // if ($usuario_admin != 1) :
        //     $msg = "Download não permitido.";
        //     Session::put('status.msg', $msg);
        //     return redirect(getenv('APP_URL') . '/painel/acompanhamento');
        //     die();
        // endif;


        // $data = array();

        // $data_inicio_db = $inicio;
        // $data_inicio = $inicio;
        // $data_fim_db = $fim;
        // $data_fim = $fim;


        // if ($usuario == 'all') :
        //     $registros = Ponto::where('data', '>=', $data_inicio_db)->where('data', '<=', $data_fim_db)->with('usuario')->orderBy('data', 'ASC')->orderBy('entrada', 'ASC')->get();
        // else :

        //     $usuario_selecionado = Usuario::where(['nome' => $usuario])->first();

        //     $registros = Ponto::where(['usuario_id' => $usuario_selecionado->id])->where('data', '>=', $data_inicio_db)->where('data', '<=', $data_fim_db)->with('usuario')->orderBy('data', 'ASC')->orderBy('entrada', 'ASC')->get();
        // endif;

        // foreach ($registros as $registro) :
        //     $data[$registro->usuario->nome][] = $registro;
        // endforeach;

        // return view('pontoeletronico/acompanhamento/index-download')->with('data_inicio', $data_inicio)->with('data_fim', $data_fim)->with('data', $data);
    }

    public function changeValidation(Request $request)
    {
        $tipo_registo = Request::get('modal_tipo_registo');
        $registo_id = Request::get('modal_registo_id');
        $changeTo = Request::get('modal_changeTo');
        $obs_coord = Request::get('obs_coord');
        if ($tipo_registo == 'ponto') {
            $registo = Ponto::find($registo_id);
        } elseif ($tipo_registo == 'ausencia') {
            $registo = Ausencia::find($registo_id);
        }
        $registo->status = $changeTo;
        $registo->obs_coord = $obs_coord;
        $registo->save();
        $accao = $changeTo == 1 ? 'validado' : 'invalidado';
        Session::put('status.msg', 'Registo ' . $accao . ' com sucesso!');
        $periodo_proc = Carbon::createFromFormat('Y-m-d', $registo->data);
        if ($periodo_proc->day >= 16) {
            $ano_mes = $periodo_proc->copy()->addMonthNoOverflow()->format('Y-m');
        } else {
            $ano_mes = $periodo_proc->copy()->format('Y-m');
        }

        return redirect()->back()
            ->with('ano_mes', $ano_mes)
            ->with('colaborador_id', $registo->utilizador_id);
    }
}
