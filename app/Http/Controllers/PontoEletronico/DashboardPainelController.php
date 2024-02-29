<?php namespace App\Http\Controllers\PontoEletronico;


use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Utilizador;

use Request;
use Session;


class DashboardPainelController extends PontoEletronicoController {

    public function __construct()
    {
        $this->middleware('authPainelMiddleware');

    }

    public function index()
    {
        $utilizar_guuid = Session::get('login.ponto.painel.utilizador_id');
        $utilizador = Utilizador::where('guuID', $utilizar_guuid)->first();


        return view('pontoeletronico/dashboard/dashboard-painel', ['utilizador' => $utilizador]);


    }

    private static function getCurrentMonth()
    {
        $mes[1] = 'Janeiro';
        $mes[2] = 'Fevereiro';
        $mes[3] = 'Março';
        $mes[4] = 'Abril';
        $mes[5] = 'Maio';
        $mes[6] = 'Junho';
        $mes[7] = 'Julho';
        $mes[8] = 'Agosto';
        $mes[9] = 'Setembro';
        $mes[10] = 'Outubro';
        $mes[11] = 'Novembro';
        $mes[12] = 'Dezembro';

        $mes_extenso = $mes[Date('n')];
        return $mes_extenso;

    }

}
