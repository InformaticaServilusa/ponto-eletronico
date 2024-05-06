<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class CoordenadorMiddleware
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
        $coordenador = Session::get('login.ponto.painel.coordenador');
        if($coordenador == 1){
            return $next($request);
        }else{
            $url_base = getenv('APP_URL');
            echo("<script>window.location.replace(\"$url_base\");</script>");
        }
    }
}
