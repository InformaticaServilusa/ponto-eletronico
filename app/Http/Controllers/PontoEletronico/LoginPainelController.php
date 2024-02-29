<?php namespace App\Http\Controllers\PontoEletronico;


use Request;
use Session;
use App\Usuario;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\GestaoDeUtilizadores;
use Illuminate\Support\Facades\Auth;

class LoginPainelController extends PontoEletronicoController {

    protected $gestaoDeUtilizadores;

    public function __construct(GestaoDeUtilizadores $gestaoDeUtilizadores)
    {
        $this->middleware('authPainelMiddleware', ['except' => ['login']]);
        $this->gestaoDeUtilizadores = $gestaoDeUtilizadores;
    }

    public function login(){
        $username = Request::input('accountname');
        $password = Request::input('password');

        if(empty($username) OR empty($password)){
            return redirect(getenv('APP_URL').'/');
        }

        $credentials = [
            'mail' => $username,
            'password' => $password
        ];

        if(Auth::attempt($credentials)) {
            // Authentication passed...
            $utilizador = Auth::user();
            Session::put('login.ponto.painel.utilizador_nome', $utilizador->getName());
            Session::put('login.ponto.painel.utilizador_id', $utilizador->getConvertedGuid());

            //preciso de controlar aqui se é coordenador ou não é coordenador
            Session::put('login.ponto.painel.admin', false);

            $this->gestaoDeUtilizadores->findOrCreateUser($utilizador);

            return redirect(getenv('APP_URL').'/painel/dashboard');

        }else{
            $erro = "Dados inválidos, tente novamente!";
            Session::put('status.msg', $erro);
            return redirect(getenv('APP_URL').'/');
        }
    }


    public function sair(){
        Session::forget('login.ponto.painel.usuario_id');
        Session::forget('login.ponto.painel.admin');
        Session::forget('login.ponto.painel.usuario_nome');

        return redirect(getenv('APP_URL').'/');
    }
}
