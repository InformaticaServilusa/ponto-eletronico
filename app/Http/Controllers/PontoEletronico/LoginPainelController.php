<?php

namespace App\Http\Controllers\PontoEletronico;


use Session;
use App\Usuario;
use App\Http\Requests;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\GestaoDeUtilizadores;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PontoEletronico\PontoEletronicoController;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\OrganizationalUnit;

class LoginPainelController extends PontoEletronicoController
{

    protected $gestaoDeUtilizadores;

    public function __construct(GestaoDeUtilizadores $gestaoDeUtilizadores)
    {
        $this->middleware('authPainelMiddleware', ['except' => ['login']]);
        $this->gestaoDeUtilizadores = $gestaoDeUtilizadores;
    }

    public function login()
    {
        $username = Request::input('accountname');
        $password = Request::input('password');

        if (empty($username) or empty($password)) {
            return redirect(getenv('APP_URL') . '/');
        }

        $credentials = [
            'sAMAccountName' => $username,
            'password' => $password
        ];

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            $utilizador = Auth::user();
            Session::put('login.ponto.painel.utilizador_nome', $utilizador->getName());
            $db_utilizador = $this->gestaoDeUtilizadores->findOrCreateUser($utilizador);
            Session::put('login.ponto.painel.utilizador_id', $db_utilizador->id);
            Session::put('login.ponto.painel.utilizado_regime', $db_utilizador->regime);
            $mes_atual = Date('m');
            $ano_atual = date('Y');
            if(Date('d') < 16){
                $mes_atual = Date('m', strtotime('-1 month'));
            }

            return redirect(getenv('APP_URL') . '/painel/dashboard/' . $ano_atual . '/' . $mes_atual);
        } else {
            $erro = "Dados inválidos, tente novamente!";
            Session::put('status.msg', $erro);
            return redirect(getenv('APP_URL') . '/');
        }
    }


    public function sair()
    {
        Session::unset();

        return redirect(getenv('APP_URL') . '/');
    }
}
