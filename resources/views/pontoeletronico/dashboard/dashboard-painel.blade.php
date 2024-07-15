@extends('pontoeletronico.painel')
@section('conteudo')
    <?php
    // $ano_mes_atual = \Carbon\Carbon::createFromFormat('Y-m', $ano_mes_atual);
    // $mes_extenso_atual = ucfirst($ano_mes_atual->locale('pt')->monthName);
    // $mes_extenso_seguinte = ucfirst($ano_mes_atual->copy()->addMonth()->locale('pt')->monthName);
    // $mes_extenso_prev = ucfirst($ano_mes_atual->copy()->subMonth()->locale('pt')->monthName);

    // $startDateString = sprintf('%04d-%02d-%02d', $prev_mes->format('Y'), $prev_mes->format('m'), 16);
    // $startDate = Carbon\Carbon::createFromFormat('Y-m-d', $startDateString);
    // $endDate = $startDate->copy()->addMonth()->subDay();
    //$periodo = Carbon\CarbonPeriod::create($data_inicio, $data_fim);
    $data_fim = Carbon\Carbon::createFromFormat('Y-m-d', $data_fim);
    $data_inicio = Carbon\Carbon::createFromFormat('Y-m-d', $data_inicio);
    $periodo_proc = Carbon\Carbon::createFromFormat('Y-m-d', $data_fim->copy()->format('Y-m-d'));
    $periodo_proc_extenso = ucfirst($data_fim->copy()->locale('pt')->monthName);
    $mes_extenso_seguinte = ucfirst($data_fim->copy()->addMonth()->locale('pt')->monthName);
    $mes_extenso_prev = ucfirst($data_fim->copy()->subMonth()->locale('pt')->monthName);
    $mes_extenso_atual = ucfirst($data_fim->copy()->locale('pt')->monthName);
    //dd($periodo, $periodo_proc,  $periodo_proc->copy()->addMonth()->format('Y-m'), $periodo_proc_extenso, $mes_extenso_seguinte, $mes_extenso_prev);
    ?>
    <script>
        function submeterDiaTrabalho(dia) {
            var regime = {{ $utilizador->regime }};

            document.querySelector('input[name="data"]').value = JSON.stringify(dia);
            if (regime == 3) {
                event.preventDefault();
                $('#modal-selecionar-dias').modal('show');
            } else {
                //Se for outro regime submete o form
                document.querySelector('form[name="form-entrada-trabalho"]').submit();
            }
        }

        function registarDiasTrabalho() {
            Livewire.emit('setTipoEntrada', 'trabalho');
            $('#create-ponto-atipico').modal('show');

        }

        function registarDiasFolga() {
            Livewire.emit('setTipoEntrada', 'ausencia');
            $('#create-ponto-atipico').modal('show');
        }

        function emitModalAusencia(dia) {
            Livewire.emit('setTipoEntrada', 'ausencia');
            Livewire.emit('setDataSubmissao', dia);
            //set data_submissao value to dia and disable it
            $('#create-ponto-atipico').modal('show');

        }
    </script>

    <section class='content'>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $utilizador->nome }}</h3>
            </div>
            <div class="box-body">
                <strong><i class="fas fa-user-tie margin-r-5"></i> Cargo:</strong>
                {{ $utilizador->cargo ?? 'Não definido' }} <br>
                <strong><i class="fas fa-user-secret margin-r-5"></i> Coordenador:</strong>
                {{ $utilizador->coordenador->nome ?? 'Não definido' }}<br>
                <strong><i class="fas fa-map-marker-alt margin-r-5"></i> Local:</strong>
                {{ $utilizador->local ?? 'Não definido' }}
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border text-center">
                <div id="ano_mes_atual" style="display: none;" data-value="{{ $periodo_proc->format('Y-m') }}"></div>
                <div style="display:flex;">
                    <div class="col-md-4 text-right">
                        <a href="{{ action('PontoEletronico\DashboardPainelController@index', ['ano_mes' => $periodo_proc->copy()->subMonth()->format('Y-m')]) }}"
                            class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i>
                            {{ $mes_extenso_prev }}</a>
                    </div>
                    <div class="col-md-4">
                        <h3 class="box-title" id="mes_atual"><strong>{{ $mes_extenso_atual }}</strong></h3>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ action('PontoEletronico\DashboardPainelController@index', ['ano_mes' => $periodo_proc->copy()->addMonth()->format('Y-m')]) }}"
                            class="btn btn-sm btn-primary">{{ $mes_extenso_seguinte }} <i
                                class="fa fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="box-tools">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- TODO: Tenho de melhorar o esquemas de cor -->
            <div class="box-body">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fas fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de dias trabalhados em
                            {{ $mes_extenso_atual }}</span>
                        <span class="info-box-number">{{ $controlo_user_mes->horas_trabalhadas / 8 }} dias</span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de folgas mês em
                            {{ $mes_extenso_atual }}</span>
                        <span class="info-box-number">{{ $controlo_user_mes->horas_folga / 8  }} dias</span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de folgas trabalhadas mês em
                            {{ $mes_extenso_atual }}</span>
                        <span class="info-box-number">{{ $controlo_user_mes->folgas_trabalhadas }} dias</span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fas fa-calendar-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de faltas em
                            {{ $mes_extenso_atual }}</span>
                        <span class="info-box-number">{{ $controlo_user_mes->horas_ausencia / 8 }} dias</span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-plane"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de Férias em
                            {{ $mes_extenso_atual }}</span>
                        <span class="info-box-number">{{ $controlo_user_mes->ferias }} dias</span>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="text-center">
                    <div class="btn-group text-center">
                        <a href="javascript:void(0)" onClick="submeterDiaTrabalho({{ json_encode(date('Y-m-d')) }})"
                            class="btn btn-md btn-success" title="Inserir registo do dia de hoje"><i
                                class="fa fa-clock"></i></a>
                        <a href="javascript:void(0)" onClick="registarDiasTrabalho()" class="btn btn-md btn-success"
                            data-tipo='trabalho'><i class="fa fa-plus"></i> Registar presença</a>
                        <a href="javascript:void(0)" onClick="registarDiasFolga()" class="btn btn-md btn-warning"
                            data-tipo='folga'><i class="fa fa-plus"></i> Registar ausência</a>
                    </div>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Livro de Ponto de <strong>16 de {{ $mes_extenso_prev }} a 15
                            {{ $mes_extenso_atual }} </strong></h3>
                </div>
                <div class="box-body no-padding">
                    <table id="registosTable" class="table text-center display" style="width: 100%">
                        <thead class='thead-dark'>
                            <tr>
                                <th scope='col'></th>
                                <th scope='col'></th>
                                <th colspan=2 scope='col' class='text-center'>Manhã</th>
                                <th colspan=2 scope='col' class='text-center'>Tarde</th>
                                <th colspan=2 scope='col' class='text-center'>Noite</th>
                                <th colspan=3 scope='col'></th>
                            </tr>
                            <tr>
                                <th scope='col'>Data</th>
                                <th scope='col'>Dia da Semana</th>
                                <th scope='col'>Entrada</th>
                                <th scope='col'>Saída</th>
                                <th scope='col'>Entrada</th>
                                <th scope='col'>Saída</th>
                                <th scope='col'>Entrada</th>
                                <th scope='col'>Saída</th>
                                <th scope='col'>Validada</th>
                                <th scope='col'>Tipo</th>
                                <th scope='row' class='no-export'>Accao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periodo as $dia)
                                @php
                                    $tipo = '';
                                    $registo = null;
                                    $is_ferias = false;
                                    if (isset($registos_ponto[$dia->format('Y-m-d')])) {
                                        $registo = $registos_ponto[$dia->format('Y-m-d')];
                                        $tipo = 'ponto';
                                    } elseif (isset($registos_ausencia[$dia->format('Y-m-d')])) {
                                        $registo = $registos_ausencia[$dia->format('Y-m-d')];
                                        $tipo = 'ausencia';
                                        if ($registo->get_tipo_ausencia() == 'Ferias') {
                                            $is_ferias = true;
                                        }
                                    } elseif (isset($feriados_mes[$dia->format('Y-m-d')])) {
                                        $tipo = 'feriado';
                                    } else {
                                        $tipo = 'indefinido';
                                    }
                                @endphp
                                <tr class="@if ($tipo == 'ponto') table-success{{ trim('') }}
                                    @elseif ($tipo == 'ausencia' && !$is_ferias) table-danger{{ trim('') }}
                                    @elseif ($tipo == 'ausencia' && $is_ferias) table-warning{{ trim('') }}
                                    @elseif ($tipo == 'feriado') table-primary{{ trim('') }}
                                    @elseif ($dia->isWeekend()) table-primary{{ trim('') }} @endif"
                                    @if ($registo) data-bs-toggle="modal"
                                    data-bs-target="#display-registo-modal-{{ $tipo }}-{{ $registo->id ?? '' }}" @endif>
                                    <td scope='row'> {{ $dia->format('Y-m-d') }} </td>
                                    <td>{{ ucfirst($dia->locale('pt')->dayName) }}</td>
                                    @if ($tipo == 'ausencia' || $tipo == 'feriado')
                                        <td colspan=6>
                                            @if ($tipo == 'ausencia')
                                                @if (isset($registo->hora_inicio) && isset($registo->hora_fim))
                                                    {{ $registo->horas_ausencia }} horas de ausencia
                                                @else
                                                    Dia completo
                                                @endif
                                            @else
                                                {{ $feriados_mes[$dia->format('Y-m-d')] }}
                                            @endif
                                        </td>
                                        <td style="display: none;"></td> <!-- Dummy cell to match colspan -->
                                        <td style="display: none;"></td> <!-- Dummy cell to match colspan -->
                                        <td style="display: none;"></td> <!-- Dummy cell to match colspan -->
                                        <td style="display: none;"></td> <!-- Dummy cell to match colspan -->
                                        <td style="display: none;"></td> <!-- Dummy cell to match colspan -->
                                    @else
                                        <td>{{ $registo ? $registo->entrada_manha ?? '--:--' : '--:--' }}</td>
                                        <td>{{ $registo ? $registo->saida_manha ?? '--:--' : '--:--' }}</td>
                                        <td>{{ $registo ? $registo->entrada_tarde ?? '--:--' : '--:--' }}</td>
                                        <td>{{ $registo ? $registo->saida_tarde ?? '--:--' : '--:--' }}</td>
                                        <td>{{ $registo ? $registo->entrada_noite ?? '--:--' : '--:--' }}</td>
                                        <td>{{ $registo ? $registo->saida_noite ?? '--:--' : '--:--' }}</td>
                                    @endif
                                    @if (isset($registo->status) && $registo->status == 1)
                                        <td>✅</td>
                                    @else
                                        <td>❌</td>
                                    @endif
                                    <td>{{ $registo ? $registo->tipo_ponto->descricao ?? $registo->tipo_ausencia->descricao : '' }}
                                    </td>
                                    @if (isset($registo))
                                        <td>
                                            <div style="display:flex; justify-content: center;">
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target ="#modal-edicao-{{ $tipo }}-{{ $registo->id ?? '' }}">
                                                    <button class="btn btn-sm btn-primary"><i
                                                            class="fas fa-edit"></i></button>
                                                </a>
                                                <a href='#'
                                                    data-url="/painel/ponto/eliminar/{{ $tipo }}/{{ $registo->id }}"
                                                    data-msg="Deseja eliminar este registo?"
                                                    class="btn btn-sm btn-danger btnExluir">
                                                    <i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                        @include('pontoeletronico.dashboard.modal.edit')

                                        @include('pontoeletronico.dashboard.modal.display-registo')
                                    @else
                                        <td>
                                            <div style="display:flex; justify-content: center;">
                                                <form method='post' action="{{ route('painel.ponto.submit') }}"
                                                    name='form-entrada-trabalho'>
                                                    {{ csrf_field() }}
                                                    <input type='hidden' name='data'
                                                        value='{{ json_encode($dia->format('Y-m-d')) }}'>
                                                    <button title='Inserir dia de trabalho completo'
                                                        class="btn btn-sm btn-success"
                                                        onclick="submeterDiaTrabalho({{ json_encode($dia->format('Y-m-d')) }})"
                                                        {{ !$is_atual ? 'hidden' : '' }}>
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                    @if ($utilizador->regime == 3)
                                                        <!-- Modal Escolher Turno TODO: MELHORAR UI-->
                                                        <div class="modal fade" id="modal-selecionar-dias" tabindex="-1"
                                                            role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-md">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title text-center">Selector de
                                                                            turno
                                                                            Callcenter</h4>
                                                                        <button type="button"
                                                                            class="btn btn-default pull-right"
                                                                            data-bs-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    {{-- //TODO:ESTE MODAL SO DEVERIA APARECER UMA VEZ E NAO PARA CADA UM DOS REGISTOS --}}
                                                                    <div class="modal-body text-centered">
                                                                        <div class="row text-centered">
                                                                            @if (isset($turnos) && count($turnos) > 0)
                                                                                @foreach ($turnos as $turno)
                                                                                    <div class="col-md-4">
                                                                                        <p>{{ $turno->descricao }}</p>
                                                                                        <p>Inicio:
                                                                                            {{ $turno->entrada_manha ?? ($turno->entrada_tarde ?? ($turno->entrada_noite ?? '')) }}
                                                                                        </p>
                                                                                        <p>Saida:
                                                                                            {{ $turno->saida_manha ?? ($turno->saida_tarde ?? ($turno->saida_noite ?? '')) }}
                                                                                        </p>
                                                                                        <input type="hidden"
                                                                                            name="turno_id"
                                                                                            value="{{ $turno->id }}">
                                                                                        <button class="btn btn-primary"
                                                                                            type="submit">Selecionar
                                                                                            {{ $turno->id }}</button>
                                                                                    </div>
                                                                                @endforeach
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </form>
                                                <button type="button" title='Inserir ausência de trabalho'
                                                    class="btn btn-sm btn-warning"
                                                    onclick="emitModalAusencia({{ json_encode($dia->format('Y-m-d')) }})">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                </a>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Your other HTML content -->
        @livewire('create-ponto-atipico')
    </section>

@endsection
