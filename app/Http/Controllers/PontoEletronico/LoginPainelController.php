<?php

namespace App\Http\Controllers\PontoEletronico;


use Session;
use Exception;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\GestaoDeUtilizadores;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\OrganizationalUnit;
use App\Http\Controllers\PontoEletronico\PontoEletronicoController;
use Log;

class LoginPainelController extends PontoEletronicoController
{

    protected $gestaoDeUtilizadores;

    public function __construct(GestaoDeUtilizadores $gestaoDeUtilizadores)
    {
        $this->gestaoDeUtilizadores = $gestaoDeUtilizadores;
    }

    public function login(Request $request)
    {

        $username = $request->input('accountname');
        $password = $request->input('password');

        if (empty($username) or empty($password)) {
            return redirect()->route('login');
        }

        $credentials = [
            'sAMAccountName' => $username,
            'password' => $password
        ];
        try {
            if (Auth::attempt($credentials)) {
                // Authentication passed...
                $utilizador = Auth::user();
                Session::put('login.ponto.painel.utilizador_nome', $utilizador->getName());
                $db_utilizador = $this->gestaoDeUtilizadores->findOrCreateUser($utilizador);
                Session::put('login.ponto.painel.utilizador_id', $db_utilizador->id);
                Session::put('login.ponto.painel.utilizador_regime', $db_utilizador->regime);
                Session::put('login.ponto.painel.coordenador', $db_utilizador->_coordenador);
                Session::put('utilizador', $db_utilizador);
                Log::info("Utilizador {$username} autenticado com sucesso!");
                Session::put('status.msg', "Bem-vindo, {$utilizador->getName()}!");
                return redirect()->route('painel.dashboard');
            } else {
                $erro = "Dados inválidos, tente novamente!";
                Session::put('status.msg', $erro);
                return redirect()->route('login');
            }
        } catch (\Exception $e) {
            Log::error('LDAP Authentication Error: ' . $e->getMessage());
        }
    }
    public function sair()
    {
        Session::flush();
        $msg = "Sessão terminada.";
        Session::put('status.msg', $msg);
        return redirect()->route('login');
    }
}
