<?php

namespace App\Http\Livewire;

use App\Horario;
use App\Ausencia;
use Carbon\Carbon;
use App\Utilizador;
use App\TiposAusencia;
use Livewire\Component;
use Carbon\CarbonPeriod;
use Livewire\WithFileUploads;
use App\ControloRHUtilizadorMes;
use Illuminate\Support\Facades\Session;
use App\Ponto; // Add the missing import statement

class CreatePontoAtipico extends Component
{
    use WithFileUploads;
    public $data_submissao = "";
    public $entrada_manha;
    public $saida_manha;
    public $entrada_tarde;
    public $saida_tarde;
    public $entrada_noite;
    public $saida_noite;
    public $tipo_entrada;
    public $tipos_ausencia;
    public $tipo_ausencia = "";
    public $hora_inicio;
    public $hora_fim;
    public $obs_colab;
    public $anexo;
    public $disabled_dataInput = false;
    public $was_folga;

    protected $listeners = ['setTipoEntrada' => 'setTipoEntrada', 'setDataSubmissao' => 'setDataSubmissao'];

    public function mount()
    {
        $this->tipos_ausencia = TiposAusencia::all();
    }

    public function setTipoEntrada($tipo)
    {
        $this->tipo_entrada = $tipo;
        $this->disabled_dataInput = false;
    }

    public function setDataSubmissao($data)
    {
        $this->data_submissao = $data;
        $this->disabled_dataInput = true;
    }

    public function render()
    {
        return view('livewire.create-ponto-atipico');
    }

    public function save()
    {
        $this->data_submissao = explode(' até ', $this->data_submissao);
        if ($this->tipo_entrada == "trabalho") {
            $msg = $this->saveTrabalho();
        } else {
            $msg = $this->saveAusencia();
        }
        $ano_mes_dia = Carbon::createFromFormat('Y-m-d', now()->format('Y-m-d'));

        if ($ano_mes_dia->format('d') > 15) {
            $ano_mes_dia->addMonths(1);
        }

        Session::put('status.msg', $msg);
        return redirect(getenv('APP_URL') . '/painel/dashboard/' . $ano_mes_dia->format('Y-m'));
    }

    private function saveTrabalho()
    {
        $msg = "";
        $total_horas_trabalhadas = 0;
        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));

        //calcular numero de horas trabalhadas através de carbon.
        if (!empty($this->entrada_manha) && !empty($this->saida_manha)) {
            $total_horas_trabalhadas += Carbon::createFromFormat('H:i', $this->entrada_manha)
                ->diffInHours(Carbon::createFromFormat('H:i', $this->saida_manha));
        }

        if (!empty($this->entrada_tarde) && !empty($this->saida_tarde)) {
            $total_horas_trabalhadas += Carbon::createFromFormat('H:i', $this->entrada_tarde)
                ->diffInHours(Carbon::createFromFormat('H:i', $this->saida_tarde));
        }

        if (!empty($this->entrada_noite) && !empty($this->saida_noite)) {
            $total_horas_trabalhadas += Carbon::createFromFormat('H:i', $this->entrada_noite)
                ->diffInHours(Carbon::createFromFormat('H:i', $this->saida_noite));
        }

        //Caso nao tenha sido introduzido nenhum horario, assumir 8h segundo o regime
        if ($total_horas_trabalhadas == 0) {
            $regime = $utilizador->regime;
            if (isset($regime) && $regime != null) {
                $horario = Horario::where('id', $regime)->first();
            } else {
                $horario = Horario::where('regime_id', $regime)->first();
            }
            $total_horas_trabalhadas = 8;
        }

        //Calcular o periodo atual.
        $today = Carbon::now();
        //Se o dia for maior ou igual a 16:
        // Inicio Periodo = 16 - Mes Atual
        //Fim Periodo = 15  - Mes Atual + 1
        //Se o dia for menor que 16:
        //Inicio Periodo = 16 - Mes atual - 1
        //Fim periodo = 15 - Mes atual
        //Se a data de submissão não pertencer ao intervalo do periodo atual, erro.
        if ($today->day >= 16) {
            $inicio_periodo = Carbon::createFromFormat('Y-m-d', $today->year . '-' . $today->month . '-16');
            $fim_periodo = $inicio_periodo->copy()->addMonth()->subDay();
        } else {
            $inicio_periodo = Carbon::createFromFormat('Y-m-d', $today->year . '-' . ($today->month - 1) . '-16');
            $fim_periodo = $inicio_periodo->copy()->addMonth()->subDay();
        }
        $periodo_atual = [
            "inicio" => $inicio_periodo->format('Y-m-d'),
            "fim" => $fim_periodo->format('Y-m-d')
        ];

        $ponto_control_rh = ControloRHUtilizadorMes::firstOrCreate(
            [
                'utilizador_id' => $utilizador->id,
                'ano_mes' => substr($periodo_atual['fim'], 0, 7)
            ]
        );
        $data = [
            'data' => '',
            'entrada_manha' => $this->entrada_manha ?? null,
            'saida_manha' => $this->saida_manha ?? null,
            'entrada_tarde' => $this->entrada_tarde ?? null,
            'saida_tarde' => $this->saida_tarde ?? null,
            'entrada_noite' => $this->entrada_noite ?? null,
            'saida_noite' => $this->saida_noite ?? null,
            'tipo_ponto_id' => $this->was_folga ? 2 : 1,
            'utilizador_id' => $utilizador->id,
            'total_horas_trabalhadas' => $total_horas_trabalhadas,
            'status' => $total_horas_trabalhadas == 8 ? 1 : 0,
            'controlo_user_mes_id' => $ponto_control_rh->id,
            'obs_colab' => $this->obs_colab ?? '',
            'was_folga' => $this->was_folga ?? 0,
        ];
        if (count($this->data_submissao) > 1) {
            //Se a data de submissão inicial e/ou final não pertencer ao periodo atual, erro.
            $startDate = Carbon::createFromFormat('Y-m-d', $this->data_submissao[0]);
            $endDate = Carbon::createFromFormat('Y-m-d', $this->data_submissao[1]);
            if ($startDate->format('Y-m-d') < $periodo_atual['inicio'] || $endDate->format('Y-m-d') > $periodo_atual['fim']) {
                $msg .= "Data de submissão inválida.\\n Fora do periodo atual"; // Error message
                return $msg;
            }
            $period = CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                if (Ponto::where('data', $date->format('Y-m-d'))->where('utilizador_id', $utilizador->id)->exists() || Ausencia::where('data', $date->format('Y-m-d'))->where('utilizador_id', $utilizador->id)->exists()) {
                    $msg .= "Registo já existe no dia " . $date->format('Y-m-d') . "\\n"; // Error message
                    continue;
                }

                $data['data'] = $date->format('Y-m-d');
                $ponto = Ponto::create($data);
                if (isset($horario)) {
                    $ponto->fill($horario->toArray());
                }

                $ponto->save();
                if ($ponto) {
                    $msg .= "Registo criado com sucesso no dia " . $date->format('Y-m-d') . "\\n"; // Success message
                    if ($ponto_control_rh) {
                        if ($total_horas_trabalhadas < 8) {
                            $ponto_control_rh->horas_ausencia += 8 - $total_horas_trabalhadas;
                        }
                        $ponto_control_rh->horas_trabalhadas += $total_horas_trabalhadas;
                    } else {
                        $ponto_control_rh = new ControloRHUtilizadorMes();
                        $ponto_control_rh->fill([
                            'utilizador_id' => $utilizador->id,
                            'ano_mes' => substr($periodo_atual['fim'], 0, 7),
                        ]);
                        if ($total_horas_trabalhadas < 8) {
                            $ponto_control_rh->horas_ausencia += 8 - $total_horas_trabalhadas;
                        }
                        $ponto_control_rh->horas_trabalhadas += $total_horas_trabalhadas;
                    }
                    if ($this->was_folga) {
                        $ponto_control_rh->folgas_trabalhadas += 1;
                        $ponto->status = 0;
                        $ponto->save();
                    }
                    $ponto_control_rh->save();
                } else {
                    $msg .= "Erro ao criar o registo no dia " . $date->format('Y-m-d') . "\\n"; // Error message
                }
            }
        } else {
            if (Ponto::where('data', $this->data_submissao[0])->where('utilizador_id', $utilizador->id)->exists() || Ausencia::where('data', $this->data_submissao[0])->where('utilizador_id', $utilizador->id)->exists()) {
                $msg .= "Registo já existe no dia " . Carbon::createFromFormat('Y-m-d', $this->data_submissao[0])->format('Y-m-d'); // Error message
                return $msg;
            }
            if ($this->data_submissao[0] < $periodo_atual['inicio'] || $this->data_submissao[0] > $periodo_atual['fim']) {
                $msg .= "Data de submissão inválida.\\n"; // Error message
                return $msg;
            }
            //SE AS HORAS ESTIVEREM VAZIAS, INSERIR ATRAVÉS DO REGIME DE TRABALHO
            $data['data'] = Carbon::createFromFormat('Y-m-d', $this->data_submissao[0])->format('Y-m-d');
            try {
                $ponto = Ponto::create($data);
            } catch (\Exception $e) {
                $msg = "Erro ao criar o registo no dia " . $data['data']; // Error message
                Log::error($e->getMessage());
                return $msg;
            }
            if ($ponto) {
                if (isset($horario)) {
                    $ponto->fill($horario->toArray());
                }

                $ponto->save();
                $msg = "Registo criado com sucesso no dia " . $data['data']; // Success message
                if ($total_horas_trabalhadas < 8) {
                    $ponto_control_rh->horas_ausencia += 8 - $total_horas_trabalhadas;
                }
                $ponto_control_rh->horas_trabalhadas += $total_horas_trabalhadas;

                if ($this->was_folga) {
                    $ponto_control_rh->folgas_trabalhadas += 1;
                    $ponto->status = 0;
                    $ponto->save();
                }

                $ponto_control_rh->save();
            } else {
                $msg = "Erro ao criar o registo no dia " . $data['data']; // Error message
            }
        }
        return $msg;
    }

    private function saveAusencia()
    {
        $utilizador = Utilizador::find(Session::get('login.ponto.painel.utilizador_id'));
        $msg = "";
        if ($this->hora_inicio && $this->hora_fim) {
            $hora_ausencia = Carbon::createFromFormat('H:i', $this->hora_inicio)
                ->diffInHours(Carbon::createFromFormat('H:i', $this->hora_fim));
        } else {
            $hora_ausencia = 8;
        }

        //upload file
        if ($this->anexo != '')
            $this->anexo = $this->anexo->store('docsJustifAusencias/' . $utilizador->id, 'public');

        $today = Carbon::now();
        if ($today->day >= 16) {
            $inicio_periodo = Carbon::createFromFormat('Y-m-d', $today->year . '-' . $today->month . '-16');
            $fim_periodo = $inicio_periodo->copy()->addMonth()->subDay();
        } else {
            $inicio_periodo = Carbon::createFromFormat('Y-m-d', $today->year . '-' . ($today->month - 1) . '-16');
            $fim_periodo = $inicio_periodo->copy()->addMonth()->subDay();
        }
        $periodo_atual = [
            "inicio" => $inicio_periodo->format('Y-m-d'),
            "fim" => $fim_periodo->format('Y-m-d')
        ];

        $ponto_control_rh = ControloRHUtilizadorMes::firstOrCreate(
            [
                'utilizador_id' => $utilizador->id,
                'ano_mes' => substr($periodo_atual['fim'], 0, 7)
            ]
        );

        $data = [
            'data' => '',
            'tipo_ausencia_id' => $this->tipo_ausencia,
            'utilizador_id' => $utilizador->id,
            'hora_inicio' => $this->hora_inicio ?? null,
            'hora_fim' => $this->hora_fim ?? null,
            'obs_colab' => $this->obs_colab ?? '',
            'horas_ausencia' => $hora_ausencia,
            'status' => 1,
            'anexo' => $this->anexo,
            'controlo_user_mes_id' => $ponto_control_rh->id,
        ];

        if (count($this->data_submissao) > 1) {
            $startDate = Carbon::createFromFormat('Y-m-d', $this->data_submissao[0]);
            $endDate = Carbon::createFromFormat('Y-m-d', $this->data_submissao[1]);
            $period = CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                if (Ponto::where('data', $date->format('Y-m-d'))->exists() || Ausencia::where('data', $date->format('Y-m-d'))->exists()) {
                    $msg .= "Registo já existe no dia " . $date->format('Y-m-d') . "\\n"; // Error message
                    continue;
                }
                $data['data'] = $date->format('Y-m-d');
                $ausencia = Ausencia::create($data);
                if ($ausencia) {
                    $msg .= "Registo criado com sucesso no dia " . $date->format('Y-m-d') . "\\n"; // Success message
                    if ($ausencia->is_falta()) {
                        $ponto_control_rh->horas_ausencia += $hora_ausencia;
                    } elseif ($ausencia->is_ferias()) {
                        $ponto_control_rh->ferias += 1;
                        $ausencia->status = 0;
                        $ausencia->save();
                    } else {
                        $ponto_control_rh->horas_folga += $hora_ausencia;
                    }
                    if ($hora_ausencia < 8) {
                        $ponto_control_rh->horas_trabalhadas += 8 - $hora_ausencia;
                    }
                    $ponto_control_rh->save();
                } else {
                    $msg .= "Erro ao criar o registo no dia " . $date->format('Y-m-d') . "\\n"; // Error message
                }
            }
        } else {
            if (Ponto::where('data', $this->data_submissao[0])->where('utilizador_id', $utilizador->id)->exists() || Ausencia::where('data', $this->data_submissao[0])->where('utilizador_id', $utilizador->id)->exists()) {
                $msg .= "Registo já existe no dia " . Carbon::createFromFormat('Y-m-d', $this->data_submissao[0])->format('Y-m-d'); // Error message
            } else {
                $data['data'] = Carbon::createFromFormat('Y-m-d', $this->data_submissao[0])->format('Y-m-d');
                if (!empty($data['hora_inicio']) && !empty($data['hora_fim'])) {
                    //$data['horas_ausencia'] = diferença de horas entre hora_inicio e hora_fim usando carbon methods
                    $data['horas_ausencia'] = Carbon::createFromFormat('H:i', $data['hora_inicio'])
                        ->diffInHours(Carbon::createFromFormat('H:i', $data['hora_fim']));
                }

                $ausencia = Ausencia::create($data);
                if ($ausencia) {
                    $msg = "Registo criado com sucesso no dia " . $data['data']; // Success message
                    if ($ausencia->is_falta()) {
                        $ponto_control_rh->horas_ausencia += $hora_ausencia;
                    } elseif ($ausencia->is_ferias()) {
                        $ponto_control_rh->ferias += 1;
                        $ausencia->status = 0;
                        $ausencia->save();
                    } else {
                        $ponto_control_rh->horas_folga += $hora_ausencia;
                    }
                    if ($hora_ausencia < 8) {
                        $ponto_control_rh->horas_trabalhadas += 8 - $hora_ausencia;
                    }
                    $ponto_control_rh->save();
                } else {
                    $msg = "Erro ao criar registo no dia " . $data['data']; // Error message
                }
            }
        }
        return $msg;
    }
}
