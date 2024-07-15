<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Utilizador;

class DepRHMiddleware
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
        try{
            $ldapUser = Auth::user();
            $utilizador_db = Utilizador::where('guuID', $ldapUser->getConvertedGuid())->first();
            if(isset($utilizador_db) && $utilizador_db->_dep_rh == 1){
                return $next($request);
            }else{
                $msg =  'Não tem permissões para aceder a esta página. Contacte o suporte Informático.';
                Session::put('status.msg', $msg);
                return redirect()->back()->with($msg);
            }
        } catch(\Exception $e){
            $msg =  'Não tem permissões para aceder a esta página. Contacte o suporte Informático.';
            Session::put('status.msg', $msg);
            return redirect()->back()->with($msg);
        }
    }
}
