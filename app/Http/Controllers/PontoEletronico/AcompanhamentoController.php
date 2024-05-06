<?php

namespace App\Http\Controllers\PontoEletronico;


use Request;
use App\Ponto;
use App\Usuario;

use App\Ausencia;
use Carbon\Carbon;
use App\PontoRazao;
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
        $this->middleware('CoordenadorMiddleware');
    }
    //Quando entro dentro do index da Coordenação, o ano_mes já vem tratado de forma a que caso seja >= 16 seja do mes seguinte
    //para enquadrar com os meses de processamento salarial.
    public function index($ano_mes)
    {
        //Colaboradores sobre coordenação do utilizador logado
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        $all_coordenandos = Utilizador::where('coordenador_id', $utilizador_id)->get();
        if (isset($_POST) && !empty($_POST['utilizador']) && $_POST['utilizador'] != 'ALL') {
            $coordenandos = Utilizador::where('coordenador_id', $utilizador_id)->where('id', $_POST['utilizador'])->get();
        } else {
            $coordenandos = Utilizador::where('coordenador_id', $utilizador_id)->get();
        }

        //Trata o periodo de pagamento
        $ano_mes = Carbon::createFromFormat('Y-m', $ano_mes);
        $inicio_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes->format('Y-m') . '-16')->subMonth();
        $fim_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes->format('Y-m') . '-15');

        //Calculo das horas expectaveis de trabalho e ausencias do mes
        $horas_expectaveis_trabalhadas_mes = MesController::getHorasExpectaveisMes($ano_mes->format('Y-m'));
        $ausencias_expectaveis_mes = MesController::getWeekendsMes($ano_mes->format('Y-m'));
        $numero_feriados = count(MesController::getFeriadosMes($ano_mes->format('Y-m')));


        //get all coordenandos Ausencia and Ponto beetween $ano_mes-15 and ($ano_mes+1month)-16
        foreach ($coordenandos as $coordenando) {
            $ausencias = Ausencia::where('utilizador_id', $coordenando->id)
                ->where('data', '>=', $inicio_periodo->format('Y-m-d'))
                ->where('data', '<=', $fim_periodo->format('Y-m-d'))
                ->get();
            $pontos = Ponto::where('utilizador_id', $coordenando->id)
                ->where('data', '>=', $inicio_periodo->format('Y-m-d'))
                ->where('data', '<=', $fim_periodo->format('Y-m-d'))
                ->get();

            //Para cada um dos Coordenandos
            //Contar o numero de horas de Ponto
            //Contar o numero de Ausencias
            $horas_trabalhadas = GestaoDePontos::countHorasTrabalhadas($pontos);;
            $coordenando->setHorasMes($horas_trabalhadas);

            $numero_ausencias = GestaoDeAusencias::countAusenciasMes($ausencias);
            $coordenando->setFaltasMes($numero_ausencias);
        }

        return view(
            'pontoeletronico/coordenacao/index-painel',
            compact([
                'coordenandos',
                'horas_expectaveis_trabalhadas_mes',
                'ausencias_expectaveis_mes',
                'numero_feriados',
                'ano_mes',
                'all_coordenandos'
            ])
        );


        //$data = array();
        // if ($_POST) :

        //     $data_inicio = Request::input('data_inicio');
        //     $data_inicio_arr = explode("/", $data_inicio);
        //     $data_inicio_db = $data_inicio_arr[2] . '-' . $data_inicio_arr[1] . '-' . $data_inicio_arr[0];

        //     $data_fim = Request::input('data_fim');
        //     $data_fim_arr = explode("/", $data_fim);
        //     $data_fim_db = $data_fim_arr[2] . '-' . $data_fim_arr[1] . '-' . $data_fim_arr[0];
        //     if ($coordenador == 1) :
        //         $usuario_selecionado = Request::input('usuario');
        //         if ($usuario_selecionado == 'all') :
        //             $registros = Ponto::where('data', '>=', $data_inicio_db)->where('data', '<=', $data_fim_db)->with('usuario')->orderBy('data', 'ASC')->orderBy('entrada', 'ASC')->get();
        //         else :
        //             $registros = Ponto::where(['usuario_id' => $usuario_selecionado])->where('data', '>=', $data_inicio_db)->where('data', '<=', $data_fim_db)->with('usuario')->orderBy('data', 'ASC')->orderBy('entrada', 'ASC')->get();
        //         endif;
        //         $usuario = array();
        //         $usuarios = Usuario::orderBy('nome', 'ASC')->get();
        //     else :
        //         $registros = Ponto::where(['usuario_id' => $usuario_id])->where('data', '>=', $data_inicio_db)->where('data', '<=', $data_fim_db)->with('usuario')->orderBy('data', 'ASC')->orderBy('entrada', 'ASC')->get();
        //         $usuario = Usuario::find($usuario_id);
        //         $usuarios = array();
        //     endif;
        // else :
        //     $data_inicio_db = Date('Y') . '-' . Date('m') . '-' . '01';
        //     $data_fim_db = Date("Y-m-d");

        //     $data_inicio = '01/' . Date('m') . '/' . Date('Y');
        //     $data_fim = Date("d/m/Y");

        //     if ($coordenador == 1) :
        //         $registros = array();
        //         $usuario = array();
        //         $usuarios = Usuario::orderBy('nome', 'ASC')->get();
        //     else :
        //         $registros = Ponto::where(['usuario_id' => $usuario_id])->where('data', '>=', $data_inicio_db)->where('data', '<=', $data_fim_db)->with('usuario')->orderBy('data', 'ASC')->orderBy('entrada', 'ASC')->get();
        //         $usuario = Usuario::find($usuario_id);
        //         $usuarios = array();
        //     endif;

        // endif;


        // foreach ($registros as $registro) :

        //     $data[$registro->usuario->nome][] = $registro;

        // endforeach;


        // //        die("<PRE>" . print_r($data,1));

        // $justificativas = PontoRazao::where(['ativo' => 1])->orderBy("descricao", "ASC")->get();

        // if ($admin == 1) :
        //     return view('pontoeletronico/acompanhamento/index-admin')->with('usuario', $usuario)->with('registros', $registros)->with('usuarios', $usuarios)->with('data_inicio', $data_inicio)->with('data_fim', $data_fim)->with('justificativas', $justificativas)->with('data', $data);
        // else :
        //     return view('pontoeletronico/acompanhamento/index')->with('usuario', $usuario)->with('registros', $registros)->with('usuarios', $usuarios)->with('data_inicio', $data_inicio)->with('data_fim', $data_fim)->with('justificativas', $justificativas)->with('data', $data);
        // endif;
    }

    public function dashboardCoordenacao($ano_mes, $colaborador_id)
    {
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
        $horas_trabalhadas = GestaoDePontos::countHorasTrabalhadas($pontos);
        $numero_ausencias = GestaoDeAusencias::countAusenciasMes($ausencias);

        //TODO: CRIAR VIEW PARA MOSTRAR AS HORAS TRABALHADAS E AUSENCIAS DO COLABORADOR E RETURNAR
        return view('pontoeletronico/coordenacao/dashboard-colaborador', compact([
            'colaborador',
            'horas_trabalhadas',
            'numero_ausencias',
            'ano_mes',
            'pontos',
            'ausencias',
            'inicio_periodo',
            'fim_periodo'

        ]));
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

    public function changeValidation($registo_id, $changeTo)
    {
        $registo = Ponto::find($registo_id);
        if (!$registo) {
            $registo = Ausencia::find($registo_id);
        }
        $registo->status = $changeTo;
        $registo->save();
        $accao = $changeTo == 1 ? 'validado' : 'invalidado';
        Session::put('status.msg', 'Registo ' . $accao . ' com sucesso!');
        return redirect()->back()
            ->with('ano_mes', $registo->data->format('Y-m'))
            ->with('colaborador_id', $registo->utilizador_id);
    }
}
