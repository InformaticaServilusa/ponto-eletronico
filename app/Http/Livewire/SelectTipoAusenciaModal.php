<?php

namespace App\Http\Livewire;

use App\Ponto;
use App\Ausencia;
use Carbon\Carbon;
use App\TiposAusencia;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class SelectTipoAusenciaModal extends Component
{
    public $dia;
    public $tipo_ausencia_select;
    public $tipos_ausencia;
    protected $listeners = ['setDia' => 'setDia'];

    public function mount()
    {
        $this->tipos_ausencia = TiposAusencia::all();
    }

    public function setDia($dia)
    {
        $this->dia = $dia;
    }

    public function selectTipoAusencia($id)
    {
        $this->tipo_ausencia_select = $id;
        $this->emitUp('submitFormAusencia');
    }

    public function submitAusencia()
    {
        $data = [
            'data' => json_encode($this->dia),
            'tipo_ausencia_id' => $this->tipo_ausencia_select
        ];
        //Vou criar aqui uma ausencia
        //1o : Verificar se já existe uma ausencia/dia de trabalho para este dia
        //2o: Se existir, enviar mensagem de erro, se nao, criar ausencia
        //3o : enviar mensagem de volta para o dashboard

        $utilizador_id = Session::get('login.ponto.painel.utilizador_id');
        $ponto = Ponto::where('data', $this->dia)
            ->where('utilizador_id', $utilizador_id)
            ->first();

        $ausencia = Ausencia::where('data', $this->dia)
            ->where('utilizador_id', $utilizador_id)
            ->first();

        if ($ponto || $ausencia) {
            $msg = "Já existe um registo para o dia " . $this->dia . ".";
        } else {
            Ausencia::create([
                'data' => $this->dia,
                'tipo_ausencia_id' => $this->tipo_ausencia_select,
                'utilizador_id' => $utilizador_id
            ]);
            $msg = "Ausência no dia " . $this-> dia . " registada com sucesso!";
        }
        $data = Carbon::now();
        Session::put('status.msg', $msg);
        if ($data->format('d') > 15) {
            $data->addMonths(1);
        }
        return redirect(getenv('APP_URL') . '/painel/dashboard/' . $data->format('Y-m'));
    }


    public function render()
    {
        return view('livewire.select-tipo-ausencia-modal');
    }
}
