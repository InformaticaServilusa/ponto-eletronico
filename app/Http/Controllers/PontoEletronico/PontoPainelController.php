<?php

namespace App\Http\Controllers\PontoEletronico;


use App\Ponto;
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
    public function submeter()
    {
        //How can I manage the Session?
        if (Session::get('login.ponto.painel.utilizador_id') == null) {
            return redirect(getenv('APP_URL') . '/');
        }

        $utilizador = Utilizador::where('guuID', Session::get('login.ponto.painel.utilizador_id'))->first();
        $data_ponto = Request::input('data');
        $data_arr = explode("/",$data_ponto);
        $data_ponto = $data_arr[2].'-'.$data_arr[1].'-'.$data_arr[0];
        $ponto = Ponto::where('utilizador_id', $utilizador->id)->where('data', $data_ponto)->first();
        if ($ponto) {
            //Erro
            $msg = "Já existe um ponto submetido para este dia!";
            Session::put('status.msg', $msg);
            return redirect(getenv('APP_URL') . '/painel/dashboard');
        } else {
            $ponto = new Ponto();
            $ponto->utilizador_id = $utilizador->id;
            $ponto->data = $data_ponto;
            $ponto->entrada_manha = Request::input('entrada_manha');
            $ponto->saida_manha = Request::input('saida_manha');
            $ponto->entrada_tarde = Request::input('entrada_tarde');
            $ponto->saida_tarde = Request::input('saida_tarde');
            $ponto->colab_obs = Request::input('colab_obs');
            $ponto->status = 0;
            $ponto->save();
            $msg = "Ponto submetido com sucesso!";
            Session::put('status.msg', $msg);
            return redirect(getenv('APP_URL') . '/painel/dashboard');
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
}
