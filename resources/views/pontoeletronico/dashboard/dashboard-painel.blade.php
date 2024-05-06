@extends('pontoeletronico.painel')

@section('conteudo')
    <?php
    $ano_mes_atual = \Carbon\Carbon::createFromFormat('Y-m', $ano_mes_atual);
    $mes_extenso_atual = ucfirst($ano_mes_atual->locale('pt')->monthName);
    $mes_extenso_seguinte = ucfirst($ano_mes_atual->copy()->addMonth()->locale('pt')->monthName);
    $mes_extenso_prev = ucfirst($ano_mes_atual->copy()->subMonth()->locale('pt')->monthName);
    ?>
    <script>
        function submeterDiaTrabalho(dia) {
            var regime = {{ Session::get('login.ponto.painel.utilizado_regime') }}
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
            var data = [];
            var checkboxes = document.getElementsByName('checked_data[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                if (checkboxes[i].checked) {
                    data.push(checkboxes[i].value);
                }
            }
            if (data.length > 0) {
                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'data';
                hiddenInput.value = JSON.stringify(data);
                document.querySelector('form[name="form-entrada-trabalho"]').appendChild(hiddenInput);
                document.querySelector('form[name="form-entrada-trabalho"]').submit();
            } else {
                Livewire.emit('setTipoEntrada', 'trabalho');
                $('#create-ponto-atipico').modal('show');
            }
        }

        function registarDiasFolga() {
            var data = [];
            var checkboxes = document.getElementsByName('checked_data[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                if (checkboxes[i].checked) {
                    data.push(checkboxes[i].value);
                }
            }
            console.log(data.length);
            if (data.length > 0) {
                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'data';
                hiddenInput.value = JSON.stringify(data);
                document.querySelector('form[name="form-entrada-folga"]').appendChild(hiddenInput);
                document.querySelector('form[name="form-entrada-folga"]').submit();
            } else {
                Livewire.emit('setTipoEntrada', 'ausencia');
                $('#create-ponto-atipico').modal('show');
            }
        }
    </script>
    <section class='content'>
        @livewire('select-tipo-ausencia-modal')
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
                <div style="display:flex;">
                    <div class="col-md-4 text-right">
                        <a href="{{ action('PontoEletronico\DashboardPainelController@index', ['ano_mes_atual' => $ano_mes_atual->copy()->subMonth()->format('Y-m')]) }}"
                            class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i>
                            {{ $mes_extenso_prev }}</a>
                    </div>
                    <div class="col-md-4">
                        <h3 class="box-title "><strong>{{ $mes_extenso_atual }}</strong></h3>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ action('PontoEletronico\DashboardPainelController@index', ['ano_mes_atual' => $ano_mes_atual->copy()->addMonth()->format('Y-m')]) }}"
                            class="btn btn-sm btn-primary">{{ $mes_extenso_seguinte }} <i class="fa fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="far fa-flag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><strong>Total de horas trabalhadas em
                                {{ $mes_extenso_atual }}</strong></span>
                        <span class="info-box-number">{{ $horas_mes ?? 'X' }} horas</span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-bed"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><strong>Total de folgas em
                                {{ $mes_extenso_atual }}</strong></span>
                        <span class="info-box-number">{{ $folgas_mes ?? 'X' }} dias</span>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="text-center">
                    <div class="btn-group">
                        <a href="javascript:void(0)" onClick="submeterDiaTrabalho({{ json_encode(date('Y-m-d')) }})"
                            class="btn btn-md btn-success" title="Inserir registo do dia de hoje"><i
                                class="fa fa-clock"></i></a>
                        <a href="javascript:void(0)" onClick="registarDiasTrabalho()" class="btn btn-md btn-success"
                            data-tipo='trabalho'><i class="fa fa-plus"></i> Registar presença</a>
                        <a href="javascript:void(0)" onClick="registarDiasFolga()" class="btn btn-md btn-warning"
                            data-tipo='folga'><i class="fa fa-plus"></i> Registar ausência</a>
                        {{-- <a href="javascript:void(0)" onClick="registarJustificacao()" class="btn btn-md btn-danger"><i
                                    class="fa fa-plus"></i> Registar justificação de falta</a> --}}
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
                    <table class="table text-center">
                        <thead class='thead-dark'>
                            <tr>
                                <th scope='col'></th>
                                <th scope='col'></th>
                                <th colspan=2 scope='col'>Manhã</th>
                                <th colspan=2 scope='col'>Tarde</th>
                                @if ($utilizador->regime == 3 || $utilizador->regime == 4)
                                    <th colspan=2 scope='col'>Noite</th>
                                @endif
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
                                <th scope='row'>Accao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $startDateString = sprintf(
                                    '%04d-%02d-%02d',
                                    $prev_mes->format('Y'),
                                    $prev_mes->format('m'),
                                    16,
                                );
                                $startDate = Carbon\Carbon::createFromFormat('Y-m-d', $startDateString);
                                $endDate = $startDate->copy()->addMonth();
                                $periodo = Carbon\CarbonPeriod::create($startDate, $endDate);
                            @endphp
                            @foreach ($periodo as $dia)
                                @php
                                    $tipo = '';
                                    $registo = null;
                                    if (isset($registos_ponto[$dia->format('Y-m-d')])) {
                                        $registo = $registos_ponto[$dia->format('Y-m-d')];
                                        $tipo = 'ponto';
                                    } elseif (isset($registos_ausencia[$dia->format('Y-m-d')])) {
                                        $registo = $registos_ausencia[$dia->format('Y-m-d')];
                                        $tipo = 'ausencia';
                                    }
                                @endphp
                                <tr
                                    class="
                                @if (isset($registos_ponto[$dia->format('Y-m-d')])) table-success
                                @elseif (isset($registos_ausencia[$dia->format('Y-m-d')]))
                                    table-warning
                                @elseif ($dia->isWeekend())
                                    table-info @endif
                                    ">
                                    <td scope='row'> {{ $dia }} </td>
                                    <td>{{ ucfirst($dia->locale('pt')->dayName) }}</td>
                                    @if ($tipo == 'ausencia')
                                        <td colspan=6>
                                            @if ($registo->hora_inicio && $registo->hora_fim)
                                                {{ $registo->horas_ausencia }} horas de ausencia
                                            @else
                                                Dia completo
                                            @endif
                                        </td>
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
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target ="#modal-edicao-{{ $tipo }}-{{ $registo->id ?? '' }}">
                                                <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                                            </a>
                                            <a href='#' data-url="/painel/ponto/eliminar/{{ $registo->id }}"
                                                data-msg="Deseja eliminar este registo?"
                                                class="btn btn-sm btn-danger btnExluir">
                                                <i class="fa fa-trash"></i></a>
                                        </td>
                                        @include('pontoeletronico.dashboard.modal.edit')
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
                                                        onclick="submeterDiaTrabalho({{ json_encode($dia->format('Y-m-d')) }})">
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
                                                <a href="#tipoAusenciaModal" data-bs-toggle="modal"
                                                    wire:click="$emit('setDia', '{{ $dia->format('Y-m-d') }}')">
                                                    <button type="button" title='Inserir ausência de trabalho'
                                                        class="btn btn-sm btn-warning">
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
