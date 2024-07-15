<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use Session;
use Illuminate\Support\Facades\Auth;
use App\Utilizador;

class AutorizacaoPainelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $logged_user = Auth::user();
            $utilizador_db = Utilizador::where('guuID', $logged_user->getConvertedGuid())->first();
            if (!isset($logged_user) || !isset($utilizador_db) || (isset($utilizador_db) && !$utilizador_db->isActive())) {
                $msg =  'Não se encontra loggado, ou a sessão expirou. Por favor, faça login novamente.';
                Session::put('status.msg', $msg);
                return redirect()->route('login');
            }

            return $next($request);
        } catch (\Exception $e) {
            $msg =  'Não se encontra loggado, ou a sessão expirou. Por favor, faça login novamente.';
            Session::put('status.msg', $msg);
            return redirect()->route('login');
        }
    }
}
