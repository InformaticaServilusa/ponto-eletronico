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
        $this->middleware('authPainelMiddleware', ['except' => ['login']]);
        $this->gestaoDeUtilizadores = $gestaoDeUtilizadores;
    }

    public function login(Request $request)
    {
        $username = $request->input('accountname');
        $password = $request->input('password');

        if (empty($username) or empty($password)) {
            return redirect(getenv('APP_URL') . '/');
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
                Session::put('login.ponto.painel.coordenador', $db_utilizador->_coodenador);
                $ano_mes_atual = Carbon::now();
                if ($ano_mes_atual->day > 16) {
                    $ano_mes_atual = $ano_mes_atual->addMonth(1);
                }
                return redirect()->route('painel.dashboard', ['ano_mes_atual' => $ano_mes_atual->format('Y-m')]);
            } else {
                $erro = "Dados inválidos, tente novamente!";
                Session::put('status.msg', $erro);
                return redirect(getenv('APP_URL') . '/');
            }
        } catch (Exception $e) {
            Log::error('LDAP Authentication Error: ' . $e->getMessage());
        }
    }
    public function sair()
    {
        Session::flush();
        return redirect(getenv('APP_URL') . '/');
    }
}
