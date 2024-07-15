@php
    $url_base = getenv('URL_BASE');
    use Carbon\Carbon;
    use Carbon\CarbonPeriod;
    $ano_mes = Carbon::parse($ano_mes);
    $inicio_periodo = Carbon::parse($inicio_periodo);
    $fim_periodo = Carbon::parse($fim_periodo);
    $periodo = CarbonPeriod::create($inicio_periodo, $fim_periodo);
    $mes_extenso_atual = ucfirst($fim_periodo->copy()->locale('pt')->monthName);
@endphp
@extends('pontoeletronico.painel')
@section('conteudo')
    <!-- HEADER -->
    <section class="content-header">
        <h1>
            Painel de Coordenação do Colaborado {{ $colaborador->nome }}
        </h1>
        <!-- /HEADER -->
        <!-- Dashboard -->
        <section class="content">
            <div class="box">
                <!-- Dashboard HEADER -->
                <div class="box-header with-border d-flex justify-content-between">
                    <div>
                        <h3 class="box-title">Informações do Mês</h3>
                    </div>
                    <div class="d-flex align-self-center">
                        <a href="{{ action('PontoEletronico\AcompanhamentoController@dashboardCoordenacao', [
                            'ano_mes' => $ano_mes->copy()->subMonth()->format('Y-m'),
                            'colaborador_id' => $colaborador->id,
                        ]) }}"
                            class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i>
                            {{ ucfirst($ano_mes->copy()->subMonth()->locale('pt')->monthName) }}</a>
                    </div>
                    <div class="d-flex align-self-center">
                        <h3 class="box-title">
                            <strong>
                                {{ ucfirst($ano_mes->locale('pt')->monthName) }} de {{ $ano_mes->year }}
                            </strong>
                        </h3>
                    </div>
                    <div class="d-flex align-self-center">
                        <a href="{{ action('PontoEletronico\AcompanhamentoController@dashboardCoordenacao', [
                            'ano_mes' => $ano_mes->copy()->addMonth()->format('Y-m'),
                            'colaborador_id' => $colaborador->id,
                        ]) }}"
                            class="btn btn-sm btn-primary">{{ ucfirst($ano_mes->copy()->addMonth()->locale('pt')->monthName) }}
                            <i class="fa fa-arrow-right"></i></a>
                    </div>
                    <div class="box-tools">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <!-- /Dashboard HEADER -->
                <!-- Dashboard Info -->
                <!-- Informaçao Horas Trabalhadas-->
                <div class="box-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-green">
                            <i class="fa fa-clock-o"></i>

                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total de dias trabalhados em
                                {{ $mes_extenso_atual }}</span>
                            <span
                                class="info-box-number">{{ $controlo_user_mes ? $controlo_user_mes->horas_trabalhadas / 8 : 'X' }}
                                dias</span>
                        </div>
                    </div>
                    <!-- /Informaçao Horas Trabalhadas-->
                    <!-- Informaçao dias de folgas-->
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow">
                            <i class="fa fa-plane"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total de folgas mês em
                                {{ $mes_extenso_atual }}</span>
                            <span
                                class="info-box-number">{{ $controlo_user_mes ? $controlo_user_mes->horas_folga / 8 : 'X' }}
                                dias</span>
                        </div>
                    </div>
                    <!-- /Informaçao dias de folgas-->
                    <!-- Informaçao dias de folgas trabalhadas-->
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow">
                            <i class="fa fa-plane"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total de folgas trabalhadas mês em
                                {{ $mes_extenso_atual }}</span>
                            <span
                                class="info-box-number">{{ $controlo_user_mes ? $controlo_user_mes->folgas_trabalhadas : 'X' }}
                                dias</span>
                        </div>
                    </div>
                    <!-- /Informaçao dias de folgas trabalhadas-->
                    <!-- Informaçao dias de ausencias-->
                    <div class="info-box">
                        <span class="info-box-icon bg-red">
                            <i class="fa fa-plane"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total de faltas em
                                {{ $mes_extenso_atual }}</span>
                            <span
                                class="info-box-number">{{ $controlo_user_mes ? $controlo_user_mes->horas_ausencia / 8 : 'X' }}
                                dias</span>
                        </div>
                    </div>
                    <!-- /Informaçao dias de ausencias-->
                    <!-- Informaçao dias de ferias-->
                    <div class="info-box">
                        <span class="info-box-icon bg-blue">
                            <i class="fa fa-plane"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Férias</span>
                            <span class="info-box-number">{{ $controlo_user_mes ? $controlo_user_mes->ferias : 'X' }}
                                dias</span>
                        </div>
                    </div>
                    <!-- /Informaçao dias de folgas trabalhadas-->
                </div>
            </div>
            <!-- /Dashboard Info -->
            <!-- Tabela Registos de Presenças/Ausenças -->
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Registos referentes ao periodo de {{ $inicio_periodo->format('Y-m-d') }} a
                        {{ $fim_periodo->format('Y-m-d') }}</h3>
                </div>
                <div class="box-body no-padding">
                    <table class="table text-center">
                        <thead class='thead-dark'>
                            <tr>
                                <th scope='col'></th>
                                <th scope='col'></th>
                                <th colspan=2 scope='col'>Manhã</th>
                                <th colspan=2 scope='col'>Tarde</th>
                                <th colspan=2 scope='col'>Noite</th>
                            </tr>
                            <tr>
                                <th>Data</th>
                                <th>Dia da Semana</th>
                                <th>Entrada</th>
                                <th>Saida</th>
                                <th>Entrada</th>
                                <th>Saida</th>
                                <th>Entrada</th>
                                <th>Saida</th>
                                <th>Validada</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periodo as $dia)
                                @php
                                    $is_ferias = false;
                                    $registo = isset($pontos[$dia->format('Y-m-d')])
                                        ? $pontos[$dia->format('Y-m-d')]
                                        : $ausencias[$dia->format('Y-m-d')] ?? null;
                                    $tipo = null;
                                    if (isset($registo)) {
                                        $tipo = $registo->get_tipo_for_view();
                                        if ($tipo == 'ausencia') {
                                            $is_ferias = $registo->is_ferias();
                                        }
                                    }
                                @endphp
                                <tr class="@if ($tipo == 'ponto') table-success{{ trim('') }}
                                @elseif ($tipo == 'ausencia' && !$is_ferias) table-danger{{ trim('') }}
                                @elseif ($tipo == 'ausencia' && $is_ferias) table-warning{{ trim('') }}
                                @elseif ($tipo == 'feriado') table-primary{{ trim('') }}
                                @elseif ($dia->isWeekend()) table-primary{{ trim('') }}
                                @else '' @endif"
                                    @if (isset($registo) && !is_null($registo)) data-bs-toggle="modal"
                                data-bs-target="#display-registo-modal-{{ $tipo }}-{{ $registo->id ?? '' }}" @endif>
                                    <td>{{ $dia->format('Y-m-d') }}</td>
                                    <td>{{ ucfirst($dia->locale('pt')->dayName) }}</td>
                                    <td>{{ $registo ? $registo->entrada_manha ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->saida_manha ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->entrada_tarde ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->saida_tarde ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->entrada_noite ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->saida_noite ?? '--:--' : '--:--' }}</td>
                                    @if (isset($registo))
                                        @if ($registo->status == 1)
                                            <td>
                                                <a href="#" data-bs-registo-id="{{ $registo->id }}"
                                                    data-bs-change-to="0"
                                                    data-bs-tipo-registo="{{ isset($registo->tipo_ponto_id) ? 'ponto' : 'ausencia' }}"
                                                    data-bs-toggle="modal" data-bs-target="#change_validation_modal"
                                                    id='change_validation' style="text-decoration: none;">✅</a>
                                            </td>
                                        @else
                                            <td>
                                                <a <a href="#" data-bs-registo-id="{{ $registo->id }}"
                                                    data-bs-change-to="1"
                                                    data-bs-tipo-registo="{{ isset($registo->tipo_ponto_id) ? 'ponto' : 'ausencia' }}"
                                                    data-bs-toggle="modal" data-bs-target="#change_validation_modal"
                                                    id='change_validation' style="text-decoration: none;">❌</a>
                                            </td>
                                        @endif
                                        @include('pontoeletronico.dashboard.modal.display-registo')
                                    @else
                                        <td></td>
                                    @endif
                                    <td>{{ $registo ? $registo->tipo_ponto->descricao ?? $registo->tipo_ausencia->descricao : '' }}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /Tabela Registos de Presenças/Ausenças -->
            <div class="modal fade" id="change_validation_modal" tabindex="-1" role="dialog"
                aria-labelledby="change_validation_modal_label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="change_validation_modal_label">Alterar Validação</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <form id="change_validation_form" method="POST"
                            action="{{ route('painel.coordenacao.changeValidation') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="obs_coord">Observação</label>
                                    <textarea class="form-control" id="obs_coord" name="obs_coord" rows="3"></textarea>
                                </div>
                                <input type="hidden" name="modal_registo_id" id="registo_id">
                                <input type="hidden" name="modal_changeTo" id="changeTo">
                                <input type="hidden" name="modal_tipo_registo" id="tipo_registo">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary">Alterar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                var myModalEl = document.getElementById('change_validation_modal')
                myModalEl.addEventListener('shown.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var registo_id = button.getAttribute('data-bs-registo-id')
                    var changeTo = button.getAttribute('data-bs-change-to')
                    var tipo_registo = button.getAttribute('data-bs-tipo-registo')

                    document.getElementById('registo_id').value = registo_id
                    document.getElementById('changeTo').value = changeTo
                    document.getElementById('tipo_registo').value = tipo_registo
                })
            </script>
        </section>
    @endsection
