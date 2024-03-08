<?php

namespace App\Http\Controllers\PontoEletronico;


use DateTime;
use App\Ponto;
use App\Horario;

use App\Usuario;
use App\Utilizador;
use App\PontoAjuste;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;


class PontoPainelController extends PontoEletronicoController
{
    public function __construct()
    {
        $this->middleware('authPainelMiddleware');
    }

    public function submeterTrabalho()
    {
        $data = json_decode(Request::input('data'));
        if (Session::get('login.ponto.painel.utilizador_id') == null) {
            return redirect(getenv('APP_URL') . '/');
        }

        if (is_array($data)) {
            foreach ($data as $processData) {
                $this->processDataTrabalho($processData);
            }
        } else {
            $this->processDataTrabalho($data);
        }

        $msg = "Ponto submetido com sucesso!";
        return $this->redirectDashboard($msg);
    }

    private function processDataTrabalho($data)
    {
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');

        $utilizador = Utilizador::where('id', $utilizador_id)->first();
        $data = Date('Y/m/d', strtotime($data));
        $data_ponto = $data;
        $data_arr = explode("/", $data_ponto);
        $data_ponto = $data_arr[2] . '-' . $data_arr[1] . '-' . $data_arr[0];
        $ponto = Ponto::where('utilizador_id', $utilizador->id)->where('data', $data_ponto)->first();
        $horario = Horario::where('regime_id', $utilizador->regime)->first();
        if ($ponto) {
            //Erro
            $msg = "Já existe um ponto submetido para este dia!";
            $this->redirectDashboard($msg);
        } else {
            $ponto = new Ponto();
            $ponto->utilizador_id = $utilizador->id;
            $ponto->data = $data;
            //$ponto->entrada_manha virá do modal de horario.
            $ponto->fill($horario->toArray());
            $ponto->status = 1;
            $ponto->tipo_ponto_id = 1;
            $ponto->save();
        }
    }

    public function submeterFolga()
    {
        $data = json_decode(Request::input('data'));
        $utilizador_id = $this->checkSession();

        if (is_array($data)) {
            foreach ($data as $processData) {
                $this->processDataFolga($processData);
            }
        } else {
            $this->processDataFolga($data);
        }

        $msg = "Folga submetida com sucesso!";
        return $this->redirectDashboard($msg);
    }

    private function processDataFolga($data)
    {
        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        $utilizador = Utilizador::where('id', $utilizador_id)->first();
        $data = Date('Y/m/d', strtotime($data));
        $data_ponto = $data;
        $data_arr = explode("/", $data_ponto);
        $data_ponto = $data_arr[2] . '-' . $data_arr[1] . '-' . $data_arr[0];
        $ponto = Ponto::where('utilizador_id', $utilizador->id)->where('data', $data_ponto)->first();
        if ($ponto) {
            //Erro
            $msg = "Já existe um ponto submetido para este dia!";
            $this->redirectDashboard($msg);
        } else {
            $ponto = new Ponto();
            $ponto->utilizador_id = $utilizador->id;
            $ponto->data = $data;
            $ponto->status = 1;
            $ponto->tipo_ponto_id = 2;
            $ponto->save();
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

    public function editar()
    {
        //TODO: QUANDO UM REGISTO EH EDITADO, PRECISA SER FEITA UMA LOGICA PARA SABER SE ELE EH VALIDADO AUTOMATICAMENTE OU NAO!
        $utilizador_id = $this->checkSession();
        $ponto_id = Request::input('registo_id');
        $ponto = Ponto::where('id', $ponto_id)->first();
        if(!$ponto){
            $msg = "Registo não encontrado!";
            return $this->redirectDashboard($msg);
        }
        $entrada_manha = Request::input('entrada_manha');
        $saida_manha = Request::input('saida_manha');
        $entrada_tarde = Request::input('entrada_tarde');
        $saida_tarde = Request::input('saida_tarde');
        //TODO: EQUACIONAR OS TURNOS NOITE CALLCENTER
        // $entrada_noite = Request::input('entrada_noite');
        // $saida_noite = Request::input('saida_noite');
        $colab_obs = Request::input('colab_obs');
        $ponto->entrada_manha = $entrada_manha;
        $ponto->saida_manha = $saida_manha;
        $ponto->entrada_tarde = $entrada_tarde;
        $ponto->saida_tarde = $saida_tarde;
        // $ponto->entrada_noite = $entrada_noite;
        // $ponto->saida_noite = $saida_noite;
        $ponto->colab_obs = $colab_obs;
        $ponto->save();
        $msg = "Registo editado com sucesso!";
        return $this->redirectDashboard($msg);
    }

    public function eliminar($ponto_id)
    {
        $utilizador_id = $this->checkSession();
        $ponto = Ponto::where('id', $ponto_id)->first();
        if(!$ponto){
            $msg = "Registo não encontrado!";
            return $this->redirectDashboard($msg);
        }
        $ponto->delete();
        $msg = "Registo eliminado com sucesso!";
        return $this->redirectDashboard($msg);
    }

    private function redirectDashboard($msg)
    {
        Session::put('status.msg', $msg);
        $ano = new DateTime();

        if($ano->format('d') < 16){
            $ano->modify('-1 month');
        }

        $mes = $ano->format('m');
        $ano = $ano->format('Y');
        return redirect(getenv('APP_URL') . '/painel/dashboard/' . $ano . '/' . $mes);
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
