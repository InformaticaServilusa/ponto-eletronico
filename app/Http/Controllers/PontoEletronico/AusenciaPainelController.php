<?php

namespace App\Http\Controllers\PontoEletronico;

use App\Services\GestaoDeAusencias;
use App\Http\Requests\AusenciaStoreRequest;

class AusenciaPainelController extends PontoEletronicoController
{
    public function __construct() {
        $this->middleware('authPainelMiddleware');
    }

    public function editar(AusenciaStoreRequest $request)
    {
        $gestaoAusencias = new GestaoDeAusencias();
        return $gestaoAusencias->editarAusencia($request);
    }
}
