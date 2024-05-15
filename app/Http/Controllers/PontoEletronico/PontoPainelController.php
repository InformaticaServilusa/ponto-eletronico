<?php

namespace App\Http\Controllers\PontoEletronico;


use DateTime;
use App\Ponto;
use App\Horario;

use App\Usuario;
use App\Ausencia;
use Carbon\Carbon;
use App\Utilizador;
use App\PontoAjuste;
use Illuminate\Http\Request;
use App\Services\GestaoDePontos;
use App\Services\GestaoDeAusencias;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\PontoStoreRequest;
use App\Http\Requests\AusenciaStoreRequest;


class PontoPainelController extends PontoEletronicoController
{
    public function __construct()
    {
        $this->middleware('authPainelMiddleware');
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
        //TODO: Já não existe checkboxes para inserção de multiplos, logo,refactor
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
            $fim_periodo =$inicio_periodo->copy()->addMonth()->subDay();
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
        //TODO: Melhorar para que as mensagens de submissao/erro tenham um cabeçalho e uma lista de datas em vez de varios cabeçalhos e varias datas
        if ($ponto || $ausencia) {
            $msg = "Já existe um ponto para o dia " . $data . "\\n";
            return $msg;
        } elseif ($data > $periodo_atual['fim' ] || $data < $periodo_atual['inicio']) {
            $msg = "Data inválida!\\n Fora do periodo atual.\\n";
            return $msg;
        }else {
            $ponto = new Ponto();
            $ponto->utilizador_id = $utilizador->id;
            $ponto->data = $data;
            $ponto->fill($horario->toArray());
            $ponto->status = 1;
            $ponto->tipo_ponto_id = 1;
            $ponto->save();
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
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        $utilizador = Utilizador::where('id', $utilizador_id)->first();
        $data = Carbon::createFromFormat('Y-m-d', $data);
        $ponto = Ponto::where('utilizador_id', $utilizador->id)->where('data', $data->toDateString())->first();
        $ausencia = Ausencia::where('utilizador_id', $utilizador->id)->where('data', $data->toDateString())->first();
        if ($ponto || $ausencia) {
            $msg = "Já existe um" . ($ponto !== null ? " Ponto" : "a Ausencia") . " para este dia!";
            return $this->redirectDashboard($msg);
        } else {
            $ausencia = new Ausencia();
            $ausencia->utilizador_id = $utilizador->id;
            $ausencia->data = $data;
            $ausencia->tipo_ausencia_id = $tipo_ausencia;
            $ausencia->status = 1;
            $ausencia->save();
            $msg = "Ausência submetida com sucesso!";
            return $this->redirectDashboard($msg);
        }
    }

    public function ajuste()
    {

        $url_base = getenv('APP_URL');

        $usuario_id = Session::get('login.ponto.painel.usuario_id');

        $ponto_id = Request::input('ponto_id');
        $tipo = Request::input('tipo');
        $data = Request::input('data');
        $hora = Request::input('hora');
        $hora_entrada = Request::input('hora_entrada');
        $hora_saida = Request::input('hora_saida');
        $justificativa = Request::input('justificativa');
        $anexo = Request::input('anexo');

        $data_arr = explode("/", $data);
        $data = $data_arr[2] . '-' . $data_arr[1] . '-' . $data_arr[0];


        $arquivo_anexo = $_FILES["anexo"];
        $varArquivo_anexo = $arquivo_anexo["name"];
        if ($varArquivo_anexo != '') :
            $arquivo_nome_final_anexo = $this->upload('../public/upload/razao/', $_FILES['anexo']);
        endif;


        if ($tipo == 'entrada' or $tipo == 'saida') :

            $ajuste = new PontoAjuste();
            $ajuste->ponto_id = $ponto_id;
            $ajuste->usuario_id = $usuario_id;
            $ajuste->ponto_ajuste_id = 0;
            $ajuste->tipo = $tipo;
            $ajuste->data = $data;
            $ajuste->hora = $hora;
            $ajuste->ponto_razao_id = $justificativa;
            $ajuste->status = 0;
            if ($varArquivo_anexo != '') :
                $ajuste->anexo = $arquivo_nome_final_anexo;
            endif;

            $ajuste->save();


            if ($tipo == 'entrada') :
                $ponto = Ponto::find($ponto_id);
                $ponto->entrada = $hora;
                $ponto->entrada_status = 1;
                $ponto->save();
            endif;

            if ($tipo == 'saida') :
                $ponto = Ponto::find($ponto_id);
                $ponto->saida = $hora;
                $ponto->saida_status = 1;
                $ponto->save();
            endif;

        endif;


        $msg = "Registro salvo com sucesso!";
        Session::put('status.msg', $msg);

        return redirect(getenv('APP_URL') . '/painel/ajuste');
    }

    public function editar(PontoStoreRequest $request)
    {
        $tipo_registo = $request->input('registo_tipo');
        $gestaoPontos = new GestaoDePontos();
        return $gestaoPontos->editarPonto($request);
    }

    public function eliminar($ponto_id)
    {
        $utilizador_id = $this->checkSession();
        $ponto = Ponto::where('id', $ponto_id)->first();

        if (!$ponto) {
            $ausencia = Ausencia::where('id', $ponto_id)->first();
            if (!$ausencia) {
                $msg = "Registo não encontrado!";
                return $this->redirectDashboard($msg);
            }
            $ausencia->delete();
            $msg = "Registo eliminado com sucesso!";
            return $this->redirectDashboard($msg);
        }
        $ponto->delete();
        $msg = "Registo eliminado com sucesso!";
        return $this->redirectDashboard($msg);
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
